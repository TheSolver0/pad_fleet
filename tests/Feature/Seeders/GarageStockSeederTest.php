<?php

namespace Tests\Feature\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Stock;
use Database\Seeders\GarageStockSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GarageStockSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_the_complete_garage_inventory(): void
    {
        $this->seed(GarageStockSeeder::class);

        $this->assertSame(304, Article::where('reference', 'like', 'GAR-%')->count());
        $this->assertSame(304, Stock::where('location', 'garage')->count());
        $this->assertSame(9.5, Stock::whereHas(
            'article',
            fn ($query) => $query->where('reference', 'GAR-0169'),
        )->value('quantity'));
    }

    public function test_it_is_idempotent_and_preserves_later_stock_adjustments(): void
    {
        $this->seed(GarageStockSeeder::class);

        $stock = Stock::whereHas(
            'article',
            fn ($query) => $query->where('reference', 'GAR-0001'),
        )->firstOrFail();
        $stock->update(['quantity' => 27]);

        $this->seed(GarageStockSeeder::class);

        $this->assertSame(304, Article::where('reference', 'like', 'GAR-%')->count());
        $this->assertSame(304, Stock::where('location', 'garage')->count());
        $this->assertSame(27, $stock->fresh()->quantity);
    }

    public function test_it_reuses_an_existing_category_with_the_same_name(): void
    {
        $category = ArticleCategory::create([
            'name' => 'Lubrifiants et fluides',
            'code' => 'LF',
            'is_active' => true,
        ]);

        $this->seed(GarageStockSeeder::class);

        $this->assertDatabaseCount('article_categories', 11);
        $this->assertSame(
            $category->id,
            Article::where('reference', 'GAR-0169')->value('article_category_id'),
        );
    }
}
