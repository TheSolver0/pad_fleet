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
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('mechanic_id')->nullable()->constrained('mechanics')->onDelete('set null');
            $table->string('reference')->unique();
            $table->date('diagnostic_date');
            $table->string('user_name');
            $table->string('user_role');
            $table->integer('km_arrival');
            $table->text('observations')->nullable();
            
            // Constats par catégorie
            $table->text('engine_issues')->nullable();
            $table->text('suspension_transmission')->nullable();
            $table->text('braking_system')->nullable();
            $table->text('electronics_electricity')->nullable();
            $table->text('bodywork_paint')->nullable();
            $table->text('air_conditioning')->nullable();
            $table->text('other_issues')->nullable();
            
            // Travaux recommandés
            $table->text('internal_works')->nullable();
            $table->text('external_works')->nullable();
            $table->text('conclusion')->nullable();
            
            // Signatures
            $table->string('mechanic_signature')->nullable();
            $table->string('maintenance_manager_signature')->nullable();
            $table->string('vehicle_manager_signature')->nullable();
            
            $table->timestamps();
            
            $table->index(['vehicle_id', 'diagnostic_date']);
            $table->index('mechanic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostics');
    }
};
