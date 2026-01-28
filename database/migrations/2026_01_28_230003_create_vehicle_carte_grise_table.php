<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_carte_grise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('reference_number', 100)->nullable()->comment('N° récépissé / Identifiant');
            $table->date('issued_at')->nullable()->comment('Délivrance');
            $table->date('expires_at')->nullable()->comment('Expiration');
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->text('notes')->nullable()->comment('Autres détails importants');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_carte_grise');
    }
};
