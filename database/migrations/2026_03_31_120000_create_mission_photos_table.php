<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->enum('type', ['before', 'after'])->default('before'); // before mission or after mission
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->date('taken_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_photos');
    }
};