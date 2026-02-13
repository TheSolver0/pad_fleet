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
        Schema::create('used_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('restrict');
            $table->integer('quantity'); // quantité de pièces usées
            $table->text('condition')->nullable(); // état des pièces usées
            $table->text('disposition')->nullable(); // destination finale (recyclage, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_materials');
    }
};
