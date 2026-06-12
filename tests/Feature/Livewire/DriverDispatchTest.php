<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Drivers\Dispatches as DriverDispatches;
use App\Models\DriverAssignment;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class DriverDispatchTest extends FeatureTestCase
{
    public function test_can_dispatch_driver_to_direction(): void
    {
        $driver = FleetTestData::driver();
        $direction = FleetTestData::direction(['name' => 'Direction Technique', 'code' => 'DT']);

        Livewire::test(DriverDispatches::class)
            ->set('driver_id', $driver->id)
            ->set('type', DriverAssignment::TYPE_DIRECTION)
            ->set('direction_id', $direction->id)
            ->set('started_at', '2026-06-01')
            ->set('ended_at', '2026-12-31')
            ->call('saveDispatch')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('driver_assignments', [
            'driver_id' => $driver->id,
            'type' => DriverAssignment::TYPE_DIRECTION,
            'direction_id' => $direction->id,
            'status' => DriverAssignment::STATUS_ACTIVE,
        ]);
    }

    public function test_can_dispatch_driver_to_person(): void
    {
        $driver = FleetTestData::driver();
        $person = FleetTestData::person(['name' => 'Directeur Général']);

        Livewire::test(DriverDispatches::class)
            ->set('driver_id', $driver->id)
            ->set('type', DriverAssignment::TYPE_PERSON)
            ->set('person_id', $person->id)
            ->set('started_at', '2026-06-01')
            ->call('saveDispatch')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('driver_assignments', [
            'driver_id' => $driver->id,
            'type' => DriverAssignment::TYPE_PERSON,
            'person_id' => $person->id,
            'direction_id' => null,
        ]);
    }

    public function test_can_end_driver_dispatch(): void
    {
        $driver = FleetTestData::driver();
        $direction = FleetTestData::direction();
        $assignment = DriverAssignment::create([
            'driver_id' => $driver->id,
            'type' => DriverAssignment::TYPE_DIRECTION,
            'direction_id' => $direction->id,
            'status' => DriverAssignment::STATUS_ACTIVE,
            'started_at' => '2026-01-01',
            'created_by' => $this->user->id,
        ]);

        Livewire::test(DriverDispatches::class)
            ->call('endDispatch', $assignment->id)
            ->assertDispatched('notify');

        $assignment->refresh();
        $this->assertSame(DriverAssignment::STATUS_ENDED, $assignment->status);
        $this->assertNotNull($assignment->ended_at);
    }
}
