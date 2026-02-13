<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Remplacer service_id par direction_id
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            
            // Ajouter direction_id
            $table->foreignId('direction_id')->nullable()->constrained('directions')->nullOnDelete();
            
            // Ajouter resource_person_id (personne à qui on donne le véhicule)
            $table->foreignId('resource_person_id')->nullable()->constrained('persons')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Restaurer service_id
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            
            // Supprimer les nouveaux champs
            $table->dropForeign(['direction_id']);
            $table->dropColumn('direction_id');
            $table->dropForeign(['resource_person_id']);
            $table->dropColumn('resource_person_id');
        });
    }
};
