<?php

namespace Tests\Unit\Models;

use App\Models\RepairPart;
use Tests\Support\FleetTestData;
use Tests\Unit\UnitTestCase;

class RepairTest extends UnitTestCase
{
    public function test_parts_cost_sums_repair_parts(): void
    {
        $repair = FleetTestData::repair();
        $article = FleetTestData::article();

        RepairPart::create([
            'repair_id' => $repair->id,
            'article_id' => $article->id,
            'quantity_used' => 2,
            'unit_price' => 5000,
            'total_price' => 10000,
            'stock_location' => 'garage',
        ]);

        RepairPart::create([
            'repair_id' => $repair->id,
            'article_id' => $article->id,
            'quantity_used' => 1,
            'unit_price' => 3000,
            'total_price' => 3000,
            'stock_location' => 'garage',
        ]);

        $this->assertSame(13000.0, $repair->fresh()->parts_cost);
    }
}
