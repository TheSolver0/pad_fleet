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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->enum('location', ['main', 'garage']); // magasin principal ou garage
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // quantité réservée pour les réparations
            $table->integer('available_quantity')->virtualAs('quantity - reserved_quantity');
            $table->timestamps();
            
            $table->unique(['article_id', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
