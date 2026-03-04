<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestataire_evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('evaluable_type'); // App\Models\Supplier, App\Models\Garage
            $table->unsignedBigInteger('evaluable_id');
            $table->decimal('quality_score', 3, 1);      // 1 à 5 - qualité des prestations
            $table->decimal('delivery_score', 3, 1)->nullable(); // 1 à 5 - respect des délais de livraison
            $table->decimal('reputation_score', 3, 1)->nullable(); // 1 à 5 - score de réputation global
            $table->text('comment')->nullable();
            $table->string('context', 255)->nullable(); // ex: "Commande #12", "Réparation véhicule XX"
            $table->date('evaluated_at');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['evaluable_type', 'evaluable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestataire_evaluations');
    }
};
