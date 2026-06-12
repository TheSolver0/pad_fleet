<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Repairs\Index as RepairsIndex;
use App\Models\Repair;
use App\Models\Vehicle;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class RepairTest extends FeatureTestCase
{
    public function test_can_create_internal_repair(): void
    {
        $vehicle = FleetTestData::vehicle();
        $garage = FleetTestData::garage();
        $mechanic = FleetTestData::mechanic();

        Livewire::test(RepairsIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('garage_id', $garage->id)
            ->set('mechanic_id', $mechanic->id)
            ->set('type', Repair::TYPE_INTERNAL)
            ->set('description', 'Vidange moteur et remplacement filtres')
            ->set('repair_type', 'entretien')
            ->set('priority', Repair::PRIORITY_MEDIUM)
            ->set('cost', '75000')
            ->set('started_at', '2026-06-01')
            ->call('saveRepair')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('repairs', [
            'vehicle_id' => $vehicle->id,
            'garage_id' => $garage->id,
            'type' => Repair::TYPE_INTERNAL,
            'repair_type' => 'entretien',
            'priority' => Repair::PRIORITY_MEDIUM,
        ]);
    }

    public function test_vip_vehicle_gets_urgent_priority_automatically(): void
    {
        $direction = FleetTestData::direction(['name' => 'Direction Générale', 'code' => 'DG']);
        $person = FleetTestData::person([
            'name' => 'Directeur Général Test',
            'direction_id' => $direction->id,
        ]);
        $vehicle = FleetTestData::vehicle(null, [
            'assigned_person_id' => $person->id,
            'assignment_type' => Vehicle::ASSIGNMENT_TRANSPORT_VIP,
        ]);
        $garage = FleetTestData::garage();

        Livewire::test(RepairsIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->assertSet('priority', Repair::PRIORITY_URGENT)
            ->assertSet('priority_auto_locked', true)
            ->set('garage_id', $garage->id)
            ->set('type', Repair::TYPE_INTERNAL)
            ->set('description', 'Réparation prioritaire VIP')
            ->set('repair_type', 'mecanique')
            ->call('saveRepair')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('repairs', [
            'vehicle_id' => $vehicle->id,
            'priority' => Repair::PRIORITY_URGENT,
        ]);
    }
}
