<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Schedules\Index as SchedulesIndex;
use App\Models\Driver;
use App\Models\VehicleSchedule;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class VehicleScheduleTest extends FeatureTestCase
{
    public function test_can_create_vehicle_schedule(): void
    {
        $vehicle = FleetTestData::vehicle();
        $driver = Driver::create([
            'first_name' => 'Jean',
            'last_name' => 'Chauffeur',
            'phone' => '+237622222222',
            'is_available' => true,
        ]);

        Livewire::test(SchedulesIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->set('title', 'Déplacement Yaoundé')
            ->set('destination', 'Yaoundé Centre')
            ->set('departure_location', 'Douala')
            ->set('start_datetime', '2026-06-10T08:00')
            ->set('end_datetime', '2026-06-10T18:00')
            ->set('estimated_distance', '250')
            ->set('purpose', 'transport personnel')
            ->set('status', VehicleSchedule::STATUS_PLANNED)
            ->set('mileage_start', '10000')
            ->set('mileage_end', '10250')
            ->call('saveSchedule')
            ->assertHasNoErrors()
            ->assertDispatched('notify')
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('vehicle_schedules', [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'title' => 'Déplacement Yaoundé',
            'destination' => 'Yaoundé Centre',
            'status' => VehicleSchedule::STATUS_PLANNED,
            'mileage_start' => 10000,
            'mileage_end' => 10250,
        ]);
    }

    public function test_vehicle_schedule_is_listed_after_creation(): void
    {
        $vehicle = FleetTestData::vehicle();

        Livewire::test(SchedulesIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('title', 'Trajet test')
            ->set('destination', 'Bafoussam')
            ->set('start_datetime', '2026-07-01T07:00')
            ->set('end_datetime', '2026-07-01T19:00')
            ->call('saveSchedule');

        $schedule = VehicleSchedule::where('title', 'Trajet test')->first();
        $this->assertNotNull($schedule);

        Livewire::test(SchedulesIndex::class)
            ->set('search', 'Trajet test')
            ->assertSee('Trajet test')
            ->assertSee($vehicle->registration);
    }
}
