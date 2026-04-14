<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_control_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('mission_id')->nullable()->constrained('missions')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ordre_mission', 100)->nullable(); // N° de l'ordre de mission / OM
            $table->string('lieu', 100)->nullable();         // Lieu de départ
            $table->date('date_depart')->nullable();
            $table->date('date_retour')->nullable();
            $table->unsignedBigInteger('km_depart')->nullable();
            $table->unsignedBigInteger('km_retour')->nullable();
            // Docs administratifs : JSON {carte_grise, assurance, visite_technique, stationnement}
            // Chaque item : { depart: 'ok'|'absent'|null, retour: 'ok'|'absent'|null }
            $table->json('docs_administratifs')->nullable();
            // Contrôle extérieur véhicule
            $table->json('controle_exterieur')->nullable();
            // Compartiment moteur
            $table->json('compartiment_moteur')->nullable();
            // Contrôle des fonctionnalités
            $table->json('controle_fonctionnalites')->nullable();
            // Outillages / accessoires
            $table->json('outillages')->nullable();
            $table->text('observations_depart')->nullable();
            $table->text('observations_retour')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_control_sheet_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_sheet_id')->constrained('vehicle_control_sheets')->cascadeOnDelete();
            $table->string('type', 20)->default('before'); // before | after
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('caption', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_control_sheet_photos');
        Schema::dropIfExists('vehicle_control_sheets');
    }
};
