<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Stock\Entries as StockEntries;
use App\Livewire\Portal\Stock\Index as StockIndex;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class StockEntryTest extends FeatureTestCase
{
    public function test_can_record_direct_stock_entry(): void
    {
        $article = FleetTestData::article();

        Livewire::test(StockEntries::class)
            ->set('entry_type', 'direct')
            ->set('location', 'garage')
            ->set('reference', 'BL-2026-001')
            ->set('entry_lines', [[
                'article_id' => $article->id,
                'quantity' => '10',
                'unit_price' => '4500',
            ]])
            ->call('saveEntry')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $stock = Stock::where('article_id', $article->id)->where('location', 'garage')->first();
        $this->assertNotNull($stock);
        $this->assertSame(10, $stock->quantity);

        $this->assertDatabaseHas('stock_movements', [
            'article_id' => $article->id,
            'type' => StockMovement::TYPE_ENTRY,
            'quantity' => 10,
            'reference' => 'BL-2026-001',
        ]);
    }

    public function test_stock_index_shows_low_stock_status(): void
    {
        $article = FleetTestData::article(null, ['min_stock_level' => 10]);
        $stock = FleetTestData::stock($article, 8, 'main');

        $component = Livewire::test(StockIndex::class);
        $label = $component->instance()->getStockStatusLabel($stock->load('article'));

        $this->assertStringContainsString('Stock faible', $label);
    }
}
