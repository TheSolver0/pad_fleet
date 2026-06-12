<?php

namespace Tests\Feature\Http;

use App\Models\Mission;
use App\Models\Repair;
use App\Models\Sinistre;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class ReportExportTest extends FeatureTestCase
{
    public function test_missions_excel_export_returns_spreadsheet(): void
    {
        $vehicle = FleetTestData::vehicle();
        $demandeur = FleetTestData::demandeur();

        Mission::create([
            'vehicle_id' => $vehicle->id,
            'demandeur_id' => $demandeur->id,
            'date_start' => '2026-06-05 08:00:00',
            'date_end' => '2026-06-05 18:00:00',
            'destination' => 'Edéa',
            'status' => Mission::STATUS_COMPLETED,
            'distance_km' => 120,
        ]);

        $response = $this->get(route('reports.missions.export.excel', [
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('rapport-missions-deplacements', $response->headers->get('content-disposition'));
    }

    public function test_repairs_pdf_export_returns_pdf(): void
    {
        $vehicle = FleetTestData::vehicle();
        $garage = FleetTestData::garage();
        $mechanic = FleetTestData::mechanic();

        Repair::create([
            'vehicle_id' => $vehicle->id,
            'garage_id' => $garage->id,
            'mechanic_id' => $mechanic->id,
            'type' => Repair::TYPE_INTERNAL,
            'description' => 'Réparation export test',
            'repair_type' => 'mecanique',
            'priority' => Repair::PRIORITY_MEDIUM,
            'cost' => 100000,
        ]);

        $response = $this->get(route('reports.repairs.export.pdf', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_sinistres_excel_export_returns_spreadsheet(): void
    {
        $vehicle = FleetTestData::vehicle();
        $assureur = FleetTestData::assureur();

        Sinistre::create([
            'vehicle_id' => $vehicle->id,
            'assureur_id' => $assureur->id,
            'declared_at' => now(),
            'description' => 'Sinistre export test',
            'status' => Sinistre::STATUS_DECLARED,
        ]);

        $response = $this->get(route('reports.sinistres.export.excel', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
