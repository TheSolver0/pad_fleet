<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Affectations\Index as AffectationsIndex;
use App\Models\Person;
use App\Models\Vehicle;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class AffectationTest extends FeatureTestCase
{
    public function test_can_assign_vehicle_to_person_with_period(): void
    {
        $vehicle = FleetTestData::vehicle();
        $person = FleetTestData::person();

        Livewire::test(AffectationsIndex::class)
            ->set('assignment_vehicle_id', $vehicle->id)
            ->set('assignment_person_id', $person->id)
            ->set('assignment_type', Vehicle::ASSIGNMENT_AFFECTATION)
            ->set('assignment_period_indefinite', false)
            ->set('assignment_start_at', '2026-01-01')
            ->set('assignment_end_at', '2026-12-31')
            ->call('saveAssignment')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $vehicle->refresh();
        $this->assertSame($person->id, $vehicle->assigned_person_id);
        $this->assertSame(Vehicle::ASSIGNMENT_AFFECTATION, $vehicle->assignment_type);
        $this->assertSame('2026-01-01', $vehicle->assignment_start_at->format('Y-m-d'));
        $this->assertSame('2026-12-31', $vehicle->assignment_end_at->format('Y-m-d'));
    }

    public function test_can_remove_vehicle_assignment(): void
    {
        $person = FleetTestData::person();
        $vehicle = FleetTestData::vehicle(null, [
            'assigned_person_id' => $person->id,
            'assignment_type' => Vehicle::ASSIGNMENT_DOTATION,
        ]);

        Livewire::test(AffectationsIndex::class)
            ->call('removeAssignment', $vehicle->id)
            ->assertDispatched('notify');

        $vehicle->refresh();
        $this->assertNull($vehicle->assigned_person_id);
        $this->assertNull($vehicle->assignment_type);
    }

    public function test_quick_add_person_selects_person_in_assignment_form(): void
    {
        Livewire::test(AffectationsIndex::class)
            ->call('openQuickAddPerson')
            ->set('quick_name', 'Nouvelle Ressource')
            ->set('quick_email', 'ressource@example.com')
            ->call('saveQuickPerson')
            ->assertDispatched('notify')
            ->assertSet('assignment_person_id', Person::where('email', 'ressource@example.com')->value('id'));
    }
}
