<?php

namespace Tests\Unit\Models;

use App\Models\Mission;
use Tests\Support\FleetTestData;
use Tests\Unit\UnitTestCase;

class MissionTest extends UnitTestCase
{
    public function test_compute_distance_from_km_departure_and_return(): void
    {
        $vehicle = FleetTestData::vehicle();
        $demandeur = FleetTestData::demandeur();

        $mission = Mission::create([
            'vehicle_id' => $vehicle->id,
            'demandeur_id' => $demandeur->id,
            'date_start' => '2026-06-01 08:00:00',
            'date_end' => '2026-06-01 18:00:00',
            'km_departure' => 12_000,
            'km_return' => 12_350,
            'status' => Mission::STATUS_PENDING,
        ]);

        $mission->computeDistance();

        $this->assertSame(350, $mission->fresh()->distance_km);
    }

    public function test_compute_distance_ignores_invalid_km_return(): void
    {
        $vehicle = FleetTestData::vehicle();
        $demandeur = FleetTestData::demandeur();

        $mission = Mission::create([
            'vehicle_id' => $vehicle->id,
            'demandeur_id' => $demandeur->id,
            'date_start' => '2026-06-01 08:00:00',
            'date_end' => '2026-06-01 18:00:00',
            'km_departure' => 12_000,
            'km_return' => 11_500,
            'status' => Mission::STATUS_PENDING,
        ]);

        $mission->computeDistance();

        $this->assertNull($mission->fresh()->distance_km);
    }
}
