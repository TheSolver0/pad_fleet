<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Assurances\Index as AssurancesIndex;
use App\Models\InsuranceContractGlobal;
use App\Models\InsuranceContractGlobalDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class AssuranceTest extends FeatureTestCase
{
    public function test_can_create_insurance_contract(): void
    {
        $assureur = FleetTestData::assureur();

        Livewire::test(AssurancesIndex::class)
            ->set('name', 'Marché annuel 2026')
            ->set('assureur_id', $assureur->id)
            ->set('insurer', $assureur->name)
            ->set('lot_description', 'Véhicules légers')
            ->set('start_date', '2026-01-01')
            ->set('end_date', '2026-12-31')
            ->set('optional_prime', '1500000')
            ->call('saveContract')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $contract = InsuranceContractGlobal::where('name', 'Marché annuel 2026')->first();
        $this->assertNotNull($contract);
        $this->assertFalse($contract->isExpired());
        $this->assertSame($assureur->id, $contract->assureur_id);
    }

    public function test_can_attach_vehicle_to_insurance_contract(): void
    {
        $contract = FleetTestData::insuranceContract();
        $vehicle = FleetTestData::vehicle(null, [
            'insurance_contract_global_id' => $contract->id,
        ]);

        $this->assertSame($contract->id, $vehicle->fresh()->insurance_contract_global_id);
        $this->assertTrue($contract->vehicles->contains($vehicle));
    }

    public function test_can_upload_insurance_contract_document(): void
    {
        Storage::fake('public');
        $contract = FleetTestData::insuranceContract();
        $file = UploadedFile::fake()->create('contrat.pdf', 100, 'application/pdf');

        Livewire::test(AssurancesIndex::class)
            ->call('openDocModal', $contract->id)
            ->set('contract_file', $file)
            ->call('uploadContractDoc')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $document = InsuranceContractGlobalDocument::where('insurance_contract_global_id', $contract->id)->first();
        $this->assertNotNull($document);
        Storage::disk('public')->assertExists($document->file_path);
    }
}
