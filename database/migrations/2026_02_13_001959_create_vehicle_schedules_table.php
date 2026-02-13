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
        Schema::create('vehicle_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('destination');
            $table->string('departure_location')->nullable();
            $table->datetime('start_datetime');
            $table->datetime('end_datetime');
            $table->decimal('estimated_distance', 8, 2)->nullable(); // en km
            $table->string('purpose')->nullable(); // transport personnel, livraison, mission, etc.
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->integer('mileage_start')->nullable();
            $table->integer('mileage_end')->nullable();
            $table->decimal('fuel_consumed', 8, 2)->nullable(); // en litres
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_schedules');
    }
};
