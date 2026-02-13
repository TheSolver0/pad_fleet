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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('diagnostic_id')->nullable()->constrained('diagnostics')->onDelete('set null');
            $table->foreignId('mechanic_id')->constrained('mechanics')->onDelete('cascade');
            $table->string('reference')->unique();
            $table->date('work_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('work_description');
            
            // Pièces utilisées
            $table->text('parts_used')->nullable();
            $table->text('parts_removed')->nullable();
            
            // Matériel utilisé
            $table->text('equipment_used')->nullable();
            $table->text('tools_used')->nullable();
            
            // Détails techniques
            $table->text('technical_notes')->nullable();
            $table->text('problems_found')->nullable();
            $table->text('solutions_applied')->nullable();
            
            // Validation et contrôle
            $table->text('quality_control')->nullable();
            $table->text('final_checks')->nullable();
            
            // Coûts
            $table->decimal('labor_cost', 10, 2)->nullable();
            $table->decimal('parts_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            
            // Statut
            $table->enum('status', ['pending', 'in_progress', 'completed', 'validated'])->default('pending');
            $table->text('completion_notes')->nullable();
            
            // Signatures
            $table->string('mechanic_signature')->nullable();
            $table->string('supervisor_signature')->nullable();
            $table->string('client_signature')->nullable();
            $table->date('validation_date')->nullable();
            
            $table->timestamps();
            
            $table->index(['vehicle_id', 'work_date']);
            $table->index('diagnostic_id');
            $table->index('mechanic_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
