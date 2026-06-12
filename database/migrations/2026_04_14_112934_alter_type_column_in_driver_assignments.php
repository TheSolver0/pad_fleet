<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change the `type` column from ENUM('vehicle','mission') to VARCHAR(30)
     * so it can also hold the values 'direction' and 'person'.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE driver_assignments MODIFY COLUMN `type` VARCHAR(30) NOT NULL DEFAULT 'vehicle'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        // Restore ENUM — will fail if rows contain 'direction' or 'person'
        DB::statement("ALTER TABLE driver_assignments MODIFY COLUMN `type` ENUM('vehicle','mission','direction','person') NOT NULL DEFAULT 'vehicle'");
    }
};
