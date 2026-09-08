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
        Schema::table('missions', function (Blueprint $table) {
            $table->text('compte_rendu')->nullable()->after('notes');
            $table->foreignId('compte_rendu_by')->nullable()->after('compte_rendu')->constrained('users')->nullOnDelete();
            $table->dateTime('compte_rendu_at')->nullable()->after('compte_rendu_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('compte_rendu_by');
            $table->dropColumn(['compte_rendu', 'compte_rendu_at']);
        });
    }
};
