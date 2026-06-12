<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Vehicles\Index as VehiclesIndex;
use App\Models\Direction;
use App\Models\InsuranceContractGlobal;
use App\Models\Person;
use App\Models\Vehicle;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class VehicleCreationTest extends FeatureTestCase
{
    public function test_can_create_vehicle_with_basic_fields(): void
    {
        [, $model] = FleetTestData::brandAndModel();

        Livewire::test(VehiclesIndex::class)
            ->set('registration', 'CM-999-ZZ')
            ->set('brand_id', $model->brand_id)
            ->set('vehicle_model_id', $model->id)
            ->set('mileage', '15000')
            ->set('status', Vehicle::STATUS_AVAILABLE)
            ->call('saveVehicle')
            ->assertDispatched('notify')
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('vehicles', [
            'registration' => 'CM-999-ZZ',
            'vehicle_model_id' => $model->id,
            'mileage' => 15000,
            'status' => Vehicle::STATUS_AVAILABLE,
        ]);
    }

    public function test_quick_add_direction_is_available_for_person_form(): void
    {
        Livewire::test(VehiclesIndex::class)
            ->call('openQuickAddDirection')
            ->set('quick_direction_name', 'Direction Finances')
            ->set('quick_direction_code', 'DF')
            ->call('saveQuickDirection')
            ->assertDispatched('notify')
            ->assertSet('showQuickAddDirection', false)
            ->assertSet('quick_direction_id', Direction::where('code', 'DF')->value('id'));

        $this->assertDatabaseHas('directions', [
            'name' => 'Direction Finances',
            'code' => 'DF',
        ]);
    }

    public function test_quick_add_person_selects_person_for_assignment(): void
    {
        $direction = FleetTestData::direction(['name' => 'Direction RH', 'code' => 'RH']);
        FleetTestData::department($direction, ['name' => 'Paie', 'code' => 'PAY']);

        Livewire::test(VehiclesIndex::class)
            ->call('openQuickAddPerson')
            ->set('quick_name', 'Marie Essomba')
            ->set('quick_email', 'marie@example.com')
            ->set('quick_phone', '+237611111111')
            ->set('quick_direction_id', $direction->id)
            ->call('saveQuickPerson')
            ->assertDispatched('notify')
            ->assertSet('showQuickAddPerson', false);

        $person = Person::where('email', 'marie@example.com')->first();
        $this->assertNotNull($person);
        $this->assertSame($direction->id, $person->direction_id);
        $this->assertDatabaseHas('persons', [
            'name' => 'Marie Essomba',
            'direction_id' => $direction->id,
        ]);
    }

    public function test_quick_add_insurance_creates_contract_and_links_vehicle_form(): void
    {
        $assureur = FleetTestData::assureur(['name' => 'Allianz Test', 'code' => 'ALL']);

        $component = Livewire::test(VehiclesIndex::class)
            ->call('openQuickAddInsurance')
            ->set('insurance_mode', 'new')
            ->set('ins_new_name', 'Marché test unitaire')
            ->set('ins_new_assureur_id', $assureur->id)
            ->set('ins_new_start_date', '2026-01-01')
            ->set('ins_new_end_date', '2026-12-31')
            ->call('saveQuickInsurance')
            ->assertDispatched('notify')
            ->assertSet('showQuickAddInsurance', false);

        $contract = InsuranceContractGlobal::where('name', 'Marché test unitaire')->first();
        $this->assertNotNull($contract);
        $this->assertSame($assureur->id, $contract->assureur_id);
        $this->assertSame($contract->id, $component->get('insurance_contract_global_id'));
    }

    public function test_can_create_vehicle_with_quick_person_and_insurance(): void
    {
        [, $model] = FleetTestData::brandAndModel();
        $assureur = FleetTestData::assureur(['name' => 'NSIA Test', 'code' => 'NSIA']);
        $direction = FleetTestData::direction(['name' => 'Direction Ops', 'code' => 'OPS']);

        $component = Livewire::test(VehiclesIndex::class)
            ->call('openQuickAddInsurance')
            ->set('insurance_mode', 'new')
            ->set('ins_new_name', 'Marché intégration')
            ->set('ins_new_assureur_id', $assureur->id)
            ->set('ins_new_start_date', '2026-02-01')
            ->set('ins_new_end_date', '2027-02-01')
            ->call('saveQuickInsurance');

        $contractId = $component->get('insurance_contract_global_id');

        $component
            ->call('openQuickAddPerson')
            ->set('quick_name', 'Paul Affecté')
            ->set('quick_direction_id', $direction->id)
            ->call('saveQuickPerson')
            ->set('registration', 'INT-2026-AA')
            ->set('brand_id', $model->brand_id)
            ->set('vehicle_model_id', $model->id)
            ->set('assignment_type', Vehicle::ASSIGNMENT_AFFECTATION)
            ->call('saveVehicle')
            ->assertDispatched('notify');

        $vehicle = Vehicle::where('registration', 'INT-2026-AA')->first();
        $this->assertNotNull($vehicle);
        $this->assertSame($contractId, $vehicle->insurance_contract_global_id);
        $this->assertNotNull($vehicle->assigned_person_id);
        $this->assertSame(Vehicle::ASSIGNMENT_AFFECTATION, $vehicle->assignment_type);
        $this->assertSame('Paul Affecté', $vehicle->assignedPerson->name);
    }
}
