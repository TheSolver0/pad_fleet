<?php

namespace Tests\Feature\Authorization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FleetTestData;
use Tests\TestCase;

class StockPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_routes_require_gestion_stock_permission(): void
    {
        $user = FleetTestData::user();
        FleetTestData::seedRolesAndPermissions();

        $this->actingAs($user)->get(route('stock.index'))->assertForbidden();
        $this->actingAs($user)->get(route('stock.articles'))->assertForbidden();
    }

    public function test_stock_entries_route_requires_entrees_stock_permission(): void
    {
        $userWithStockOnly = FleetTestData::userWithPermission('gestion-stock');

        $this->actingAs($userWithStockOnly)->get(route('stock.entries'))->assertForbidden();

        $userWithEntries = FleetTestData::userWithPermission('entrees-stock');
        $this->actingAs($userWithEntries)->get(route('stock.entries'))->assertOk();
    }

    public function test_repair_stock_usage_requires_sorties_stock_permission(): void
    {
        $user = FleetTestData::user();
        FleetTestData::seedRolesAndPermissions();

        $this->actingAs($user)->get(route('repairs.stock-usage'))->assertForbidden();

        $magasinier = FleetTestData::userWithPermission('sorties-stock');
        $this->actingAs($magasinier)->get(route('repairs.stock-usage'))->assertOk();
    }
}
