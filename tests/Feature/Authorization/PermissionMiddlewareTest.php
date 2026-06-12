<?php

namespace Tests\Feature\Authorization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FleetTestData;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('stock.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_without_permission_gets_forbidden(): void
    {
        $user = FleetTestData::user();
        FleetTestData::seedRolesAndPermissions();

        $this->actingAs($user)
            ->get(route('stock.index'))
            ->assertForbidden();
    }

    public function test_user_with_gestion_stock_permission_can_access_stock(): void
    {
        $user = FleetTestData::userWithPermission('gestion-stock');

        $this->actingAs($user)
            ->get(route('stock.index'))
            ->assertOk();
    }

    public function test_user_with_suivi_assurances_permission_can_access_driver_documents(): void
    {
        $user = FleetTestData::userWithPermission('suivi-assurances');

        $this->actingAs($user)
            ->get(route('drivers.documents'))
            ->assertOk();
    }

    public function test_user_with_audits_permission_can_access_audit_log(): void
    {
        $user = FleetTestData::userWithPermission('audits');

        $this->actingAs($user)
            ->get(route('audit.index'))
            ->assertOk();
    }

    public function test_gestionnaire_flotte_role_can_access_fleet_routes_without_explicit_grant(): void
    {
        $user = FleetTestData::userWithRole('Gestionnaire Flotte');

        $this->actingAs($user)
            ->get(route('missions.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('assurances.index'))
            ->assertOk();
    }
}
