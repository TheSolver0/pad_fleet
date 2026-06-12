<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Missions\Index as MissionsIndex;
use App\Models\Mission;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class MissionWorkflowTest extends FeatureTestCase
{
    public function test_mission_creation_starts_as_pending(): void
    {
        $vehicle = FleetTestData::vehicle();
        $demandeur = FleetTestData::demandeur();

        Livewire::test(MissionsIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('demandeur_id', $demandeur->id)
            ->set('date_start', '2026-06-15')
            ->set('date_end', '2026-06-16')
            ->set('destination', 'Kribi')
            ->set('raison', 'Mission officielle')
            ->set('km_departure', '10000')
            ->set('km_return', '10450')
            ->call('saveMission')
            ->assertHasNoErrors()
            ->assertDispatched('notify')
            ->assertSet('showFormModal', false);

        $mission = Mission::first();
        $this->assertNotNull($mission);
        $this->assertSame(Mission::STATUS_PENDING, $mission->status);
        $this->assertSame('Kribi', $mission->destination);
        $this->assertSame(450, $mission->distance_km);
    }

    public function test_mission_approval_workflow(): void
    {
        $vehicle = FleetTestData::vehicle();
        $demandeur = FleetTestData::demandeur();

        Livewire::test(MissionsIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('demandeur_id', $demandeur->id)
            ->set('date_start', '2026-06-20')
            ->set('date_end', '2026-06-21')
            ->set('destination', 'Bertoua')
            ->call('saveMission');

        $mission = Mission::first();
        $this->assertSame(Mission::STATUS_PENDING, $mission->status);

        Livewire::test(MissionsIndex::class)
            ->call('openApproveModal', $mission->id)
            ->set('approve_reject', true)
            ->call('approveOrReject')
            ->assertDispatched('notify');

        $mission->refresh();
        $this->assertSame(Mission::STATUS_PROGRAMMED, $mission->status);
        $this->assertSame($this->user->id, $mission->approved_by);
        $this->assertNotNull($mission->approved_at);

        Livewire::test(MissionsIndex::class)
            ->call('markInProgress', $mission->id)
            ->assertDispatched('notify');

        $mission->refresh();
        $this->assertSame(Mission::STATUS_IN_PROGRESS, $mission->status);
    }

    public function test_mission_completion_updates_distance_and_vehicle_mileage(): void
    {
        $vehicle = FleetTestData::vehicle(null, ['mileage' => 5000]);
        $demandeur = FleetTestData::demandeur();

        Livewire::test(MissionsIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('demandeur_id', $demandeur->id)
            ->set('date_start', '2026-06-25')
            ->set('date_end', '2026-06-26')
            ->set('km_departure', '5000')
            ->set('km_return', '5300')
            ->call('saveMission');

        $mission = Mission::first();

        Livewire::test(MissionsIndex::class)
            ->call('markCompleted', $mission->id)
            ->assertDispatched('notify');

        $mission->refresh();
        $vehicle->refresh();

        $this->assertSame(Mission::STATUS_COMPLETED, $mission->status);
        $this->assertSame(300, $mission->distance_km);
        $this->assertSame(5300, $vehicle->mileage);
    }

    public function test_mission_can_be_rejected(): void
    {
        $vehicle = FleetTestData::vehicle();
        $demandeur = FleetTestData::demandeur();

        Livewire::test(MissionsIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('demandeur_id', $demandeur->id)
            ->set('date_start', '2026-06-28')
            ->set('date_end', '2026-06-29')
            ->call('saveMission');

        $mission = Mission::first();

        Livewire::test(MissionsIndex::class)
            ->call('openApproveModal', $mission->id)
            ->set('approve_reject', false)
            ->call('approveOrReject');

        $mission->refresh();
        $this->assertSame(Mission::STATUS_REJECTED, $mission->status);
    }
}
