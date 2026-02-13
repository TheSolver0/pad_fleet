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
        Schema::table('insurance_contract_globals', function (Blueprint $table) {
            $table->foreignId('assureur_id')->nullable()->constrained('assureurs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_contract_globals', function (Blueprint $table) {
            $table->dropForeign(['assureur_id']);
            $table->dropColumn('assureur_id');
        });
    }
};
