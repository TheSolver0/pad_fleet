<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['insurance_contract_global_id']);
            $table->dropColumn('insurance_contract_global_id');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('insurance_contract_global_id')
                ->nullable()
                ->after('garage_id')
                ->constrained('insurance_contract_globals')
                ->nullOnDelete();
        });
    }
};
