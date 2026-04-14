<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_assignments', function (Blueprint $table) {
            // Mise à disposition d'une direction ou d'un directeur/agent
            $table->foreignId('direction_id')->nullable()->after('vehicle_id')
                ->constrained('directions')->nullOnDelete();
            $table->foreignId('person_id')->nullable()->after('direction_id')
                ->constrained('persons')->nullOnDelete();
            // Type étendu : vehicle | mission | direction | person
            // (la colonne type existe déjà, on étend les valeurs possibles)
        });
    }

    public function down(): void
    {
        Schema::table('driver_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('direction_id');
            $table->dropConstrainedForeignId('person_id');
        });
    }
};
