<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\WorkOrders\Index as WorkOrdersIndex;
use App\Models\WorkOrder;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class WorkOrderTest extends FeatureTestCase
{
    public function test_can_create_work_order_with_tasks(): void
    {
        $vehicle = FleetTestData::vehicle();
        $mechanic = FleetTestData::mechanic();
        $article = FleetTestData::article();

        Livewire::test(WorkOrdersIndex::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('mechanic_id', $mechanic->id)
            ->set('work_date', '2026-06-15')
            ->set('work_description', 'Remplacement plaquettes de frein')
            ->set('labor_cost', '25000')
            ->set('parts_cost', '24000')
            ->set('status', WorkOrder::STATUS_PENDING)
            ->set('tasks', [[
                'title' => 'Démontage roues',
                'estimated_minutes' => 30,
                'mechanic_id' => $mechanic->id,
                'is_done' => true,
            ], [
                'title' => 'Pose nouvelles plaquettes',
                'estimated_minutes' => 45,
                'mechanic_id' => $mechanic->id,
                'is_done' => false,
            ]])
            ->call('addPartLine')
            ->set('parts_lines.0.article_id', $article->id)
            ->set('parts_lines.0.quantity', '2')
            ->set('parts_lines.0.unit_price', '12000')
            ->set('parts_lines.0.part_description', 'Plaquettes avant')
            ->set('parts_lines.0.stock_location', 'garage')
            ->call('saveWorkOrder')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $workOrder = WorkOrder::first();
        $this->assertNotNull($workOrder);
        $this->assertNotNull($workOrder->reference);
        $this->assertSame(2, $workOrder->tasks()->count());
        $this->assertSame(1, $workOrder->parts()->count());
        $this->assertSame(50, $workOrder->completion_percent);
        $this->assertSame(49000.0, (float) $workOrder->total_cost);
    }

    public function test_can_validate_work_order(): void
    {
        $vehicle = FleetTestData::vehicle();
        $mechanic = FleetTestData::mechanic();

        $workOrder = WorkOrder::create([
            'vehicle_id' => $vehicle->id,
            'mechanic_id' => $mechanic->id,
            'reference' => WorkOrder::generateReference(),
            'work_date' => '2026-06-10',
            'work_description' => 'Contrôle général',
            'status' => WorkOrder::STATUS_COMPLETED,
            'completion_percent' => 90,
        ]);

        Livewire::test(WorkOrdersIndex::class)
            ->call('validateWorkOrder', $workOrder->id)
            ->assertDispatched('notify');

        $workOrder->refresh();
        $this->assertSame(WorkOrder::STATUS_VALIDATED, $workOrder->status);
        $this->assertSame(100, $workOrder->completion_percent);
        $this->assertNotNull($workOrder->validation_date);
    }
}
