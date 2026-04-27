<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_mechanic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mechanic_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['mission_id', 'mechanic_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_mechanic');
    }
};

