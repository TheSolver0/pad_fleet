<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Sinistres\Index as SinistresIndex;
use App\Models\Sinistre;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class SinistreTest extends FeatureTestCase
{
    public function test_can_declare_sinistre_linked_to_vehicle_and_assureur(): void
    {
        $vehicle = FleetTestData::vehicle();
        $assureur = FleetTestData::assureur();

        Livewire::test(SinistresIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('assureur_id', $assureur->id)
            ->set('declared_at', '2026-06-10T14:30')
            ->set('description', 'Collision arrière au carrefour')
            ->set('location', 'Douala, Akwa')
            ->set('estimated_cost', '450000')
            ->set('status', Sinistre::STATUS_DECLARED)
            ->call('saveSinistre')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('sinistres', [
            'vehicle_id' => $vehicle->id,
            'assureur_id' => $assureur->id,
            'status' => Sinistre::STATUS_DECLARED,
            'location' => 'Douala, Akwa',
        ]);
    }

    public function test_can_upload_police_report_when_declaring_sinistre(): void
    {
        Storage::fake('public');
        $vehicle = FleetTestData::vehicle();
        $file = UploadedFile::fake()->create('pv-police.pdf', 120, 'application/pdf');

        Livewire::test(SinistresIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('declared_at', '2026-06-11T09:00')
            ->set('description', 'Accrochage léger')
            ->set('police_report_file', $file)
            ->call('saveSinistre')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $sinistre = Sinistre::first();
        $this->assertNotNull($sinistre);
        $this->assertNotNull($sinistre->police_report_path);
        Storage::disk('public')->assertExists($sinistre->police_report_path);
    }
}
