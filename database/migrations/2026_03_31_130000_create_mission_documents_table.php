<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type'); // image, pdf
            $table->string('mime_type');
            $table->string('document_type')->nullable(); // ordre_mission, rapport, facture, etc.
            $table->string('caption')->nullable();
            $table->integer('file_size'); // en bytes
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_documents');
    }
};