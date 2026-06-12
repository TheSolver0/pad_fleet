<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Repairs\StockUsage;
use App\Models\RepairPart;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\UsedMaterial;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class RepairStockUsageTest extends FeatureTestCase
{
    public function test_can_record_stock_usage_for_repair(): void
    {
        $article = FleetTestData::article();
        FleetTestData::stock($article, 15, 'garage');
        $repair = FleetTestData::repair();

        Livewire::test(StockUsage::class)
            ->set('repair_id', $repair->id)
            ->set('parts', [[
                'article_id' => $article->id,
                'quantity_used' => 2,
                'unit_price' => '5000',
                'stock_location' => 'garage',
                'notes' => '',
            ]])
            ->set('used_materials', [[
                'article_id' => $article->id,
                'quantity' => 2,
                'condition' => 'usé',
                'disposition' => 'stocké',
                'notes' => '',
            ]])
            ->call('saveStockUsage')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertSame(1, RepairPart::where('repair_id', $repair->id)->count());
        $this->assertSame(1, UsedMaterial::where('repair_id', $repair->id)->count());
        $this->assertSame(13, Stock::where('article_id', $article->id)->value('quantity'));

        $this->assertDatabaseHas('stock_movements', [
            'article_id' => $article->id,
            'type' => StockMovement::TYPE_EXIT,
            'repair_id' => $repair->id,
            'quantity' => 2,
        ]);
    }

    public function test_stock_usage_rejects_mismatched_used_material_quantity(): void
    {
        $article = FleetTestData::article();
        FleetTestData::stock($article, 10, 'garage');
        $repair = FleetTestData::repair();

        Livewire::test(StockUsage::class)
            ->set('repair_id', $repair->id)
            ->set('parts', [[
                'article_id' => $article->id,
                'quantity_used' => 2,
                'unit_price' => '5000',
                'stock_location' => 'garage',
                'notes' => '',
            ]])
            ->set('used_materials', [[
                'article_id' => $article->id,
                'quantity' => 1,
                'condition' => 'usé',
                'disposition' => 'stocké',
                'notes' => '',
            ]])
            ->call('saveStockUsage')
            ->assertDispatched('notify');

        $this->assertSame(0, RepairPart::where('repair_id', $repair->id)->count());
    }
}
