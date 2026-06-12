<?php

namespace Tests\Unit\Models;

use App\Models\Vehicle;
use Carbon\Carbon;
use Tests\Support\FleetTestData;
use Tests\Unit\UnitTestCase;

class VehicleTest extends UnitTestCase
{
    public function test_compute_venal_value_depreciates_linearly(): void
    {
        Carbon::setTestNow('2028-01-01');
        [, $model] = FleetTestData::brandAndModel();

        $vehicle = Vehicle::create([
            'registration' => 'VEN-001-AA',
            'vehicle_model_id' => $model->id,
            'purchase_date' => '2024-01-01',
            'purchase_price' => 8_000_000,
            'mileage' => 0,
            'status' => Vehicle::STATUS_AVAILABLE,
        ]);

        // 4 ans sur 8 => 50% restant
        $this->assertSame(4_000_000.0, $vehicle->computeVenalValue());
    }

    public function test_compute_venal_value_returns_zero_after_depreciation_period(): void
    {
        Carbon::setTestNow('2035-01-01');
        [, $model] = FleetTestData::brandAndModel();

        $vehicle = Vehicle::create([
            'registration' => 'VEN-002-AA',
            'vehicle_model_id' => $model->id,
            'purchase_date' => '2020-01-01',
            'purchase_price' => 5_000_000,
            'mileage' => 0,
            'status' => Vehicle::STATUS_AVAILABLE,
        ]);

        $this->assertSame(0.0, $vehicle->computeVenalValue());
    }

    public function test_assignment_period_label_for_indefinite_assignment(): void
    {
        $person = FleetTestData::person();
        $vehicle = FleetTestData::vehicle(null, [
            'assigned_person_id' => $person->id,
            'assignment_type' => Vehicle::ASSIGNMENT_DOTATION,
            'assignment_start_at' => '2026-01-01',
            'assignment_end_at' => null,
        ]);

        $this->assertStringContainsString('indéfini', strtolower($vehicle->assignment_period_label));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
