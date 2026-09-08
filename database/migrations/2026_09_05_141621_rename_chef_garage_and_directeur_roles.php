<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Renomme les rôles existants pour coller à la hiérarchie Direction / Département / Service :
     * "Chef Garage" -> "Chef Service", "Directeur / Chef Service" -> "Chef Département".
     * Les utilisateurs déjà affectés à ces rôles (model_has_roles) conservent leur affectation
     * puisque seul le libellé du rôle change, pas son id.
     */
    public function up(): void
    {
        DB::table('roles')->where('name', 'Chef Garage')->update(['name' => 'Chef Service']);
        DB::table('roles')->where('name', 'Directeur / Chef Service')->update(['name' => 'Chef Département']);
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'Chef Service')->update(['name' => 'Chef Garage']);
        DB::table('roles')->where('name', 'Chef Département')->update(['name' => 'Directeur / Chef Service']);
    }
};
