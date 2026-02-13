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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('article_category_id')->constrained()->onDelete('restrict');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('unit')->default('unité'); // unité, litre, kg, mètre, etc.
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->integer('min_stock_level')->default(0);
            $table->integer('max_stock_level')->nullable();
            $table->string('tire_size')->nullable(); // pour les pneus ex: 205/55R16
            $table->json('compatible_vehicle_categories')->nullable(); // catégories de véhicules compatibles
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
