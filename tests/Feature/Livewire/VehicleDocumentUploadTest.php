<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Vehicles\Index as VehiclesIndex;
use App\Models\VehicleDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class VehicleDocumentUploadTest extends FeatureTestCase
{
    public function test_can_upload_assurance_document_for_vehicle(): void
    {
        Storage::fake('public');
        $vehicle = FleetTestData::vehicle();
        $file = UploadedFile::fake()->create('attestation-assurance.pdf', 100, 'application/pdf');

        Livewire::test(VehiclesIndex::class)
            ->call('openDocModal', $vehicle->id)
            ->set('doc_type', VehicleDocument::TYPE_ASSURANCE)
            ->set('doc_file', $file)
            ->set('doc_expires_at', '2026-12-31')
            ->call('uploadDocument')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $document = VehicleDocument::where('vehicle_id', $vehicle->id)->first();
        $this->assertNotNull($document);
        $this->assertSame(VehicleDocument::TYPE_ASSURANCE, $document->type);
        $this->assertSame('2026-12-31', $document->expires_at->format('Y-m-d'));
        Storage::disk('public')->assertExists($document->file_path);
    }
}
