<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite conserve déjà les valeurs décimales grâce à son typage dynamique.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE stocks DROP COLUMN available_quantity');
        DB::statement('ALTER TABLE stocks MODIFY quantity DECIMAL(12,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stocks MODIFY reserved_quantity DECIMAL(12,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stocks ADD available_quantity DECIMAL(12,2) GENERATED ALWAYS AS (quantity - reserved_quantity) VIRTUAL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE stocks DROP COLUMN available_quantity');
        DB::statement('ALTER TABLE stocks MODIFY quantity INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stocks MODIFY reserved_quantity INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stocks ADD available_quantity INT GENERATED ALWAYS AS (quantity - reserved_quantity) VIRTUAL');
    }
};
