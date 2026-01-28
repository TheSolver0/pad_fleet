<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('demandeur_id')->constrained('demandeurs')->cascadeOnDelete();
            $table->dateTime('date_start');
            $table->dateTime('date_end');
            $table->unsignedBigInteger('km_departure')->nullable();
            $table->unsignedBigInteger('km_return')->nullable();
            $table->unsignedBigInteger('distance_km')->nullable(); // calculé si km_departure + km_return
            $table->string('destination')->nullable();
            $table->string('status', 30)->default('pending'); // pending, approved, rejected, completed, cancelled
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
