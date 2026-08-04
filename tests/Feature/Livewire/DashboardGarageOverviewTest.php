<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Dashboard\Index;
use App\Models\Garage;
use App\Models\Repair;
use App\Models\Vehicle;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class DashboardGarageOverviewTest extends FeatureTestCase
{
    public function test_it_separates_and_lists_vehicles_managed_by_garages(): void
    {
        [, $model] = FleetTestData::brandAndModel();
        $internalGarage = FleetTestData::garage();
        $externalGarage = FleetTestData::garage([
            'name' => 'Prestataire externe',
            'type' => Garage::TYPE_EXTERNAL,
        ]);

        $internalVehicle = FleetTestData::vehicle($model, [
            'registration' => 'LT-101-AA',
            'status' => Vehicle::STATUS_REPAIR,
            'garage_id' => $internalGarage->id,
        ]);
        $externalVehicle = FleetTestData::vehicle($model, [
            'registration' => 'LT-102-AA',
            'status' => Vehicle::STATUS_REPAIR,
            'garage_id' => $externalGarage->id,
        ]);

        FleetTestData::repair($internalVehicle, $internalGarage, [
            'type' => Repair::TYPE_INTERNAL,
            'started_at' => now()->subDays(3),
        ]);
        FleetTestData::repair($externalVehicle, $externalGarage, [
            'type' => Repair::TYPE_EXTERNAL,
            'started_at' => now()->subDays(10),
            'expected_completed_at' => now()->subDay(),
        ]);

        $overview = (new Index)->getGarageOverview();

        $this->assertSame(2, $overview['total']);
        $this->assertSame(1, $overview['internal']);
        $this->assertSame(1, $overview['external']);
        $this->assertSame(1, $overview['overdue']);
        $this->assertEqualsCanonicalizing(
            ['LT-101-AA', 'LT-102-AA'],
            $overview['rows']->pluck('registration')->all(),
        );
    }
}
