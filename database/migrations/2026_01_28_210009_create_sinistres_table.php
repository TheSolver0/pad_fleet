<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sinistres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('mission_id')->nullable()->constrained('missions')->nullOnDelete();
            $table->dateTime('declared_at');
            $table->text('description');
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->string('responsibility', 100)->nullable(); // tiers, nous, partagé...
            $table->foreignId('garage_id')->nullable()->constrained('garages')->nullOnDelete();
            $table->string('status', 30)->default('declared'); // declared, in_repair, closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinistres');
    }
};
