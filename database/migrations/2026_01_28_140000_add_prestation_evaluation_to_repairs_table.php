<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            // Prestation : véhicule confié au prestataire — délai qu'on lui a donné (date limite)
            $table->dateTime('expected_completed_at')->nullable()->after('completed_at');
            // Évaluation prestataire : qualité du travail, respect du délai
            $table->decimal('quality_rating', 3, 1)->nullable()->after('expected_completed_at'); // 1 à 5
            $table->decimal('delay_rating', 3, 1)->nullable()->after('quality_rating');           // 1 à 5 (respect délai)
            $table->text('evaluation_comment')->nullable()->after('delay_rating');
            $table->dateTime('evaluated_at')->nullable()->after('evaluation_comment');
        });
    }

    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropColumn([
                'expected_completed_at',
                'quality_rating',
                'delay_rating',
                'evaluation_comment',
                'evaluated_at',
            ]);
        });
    }
};
