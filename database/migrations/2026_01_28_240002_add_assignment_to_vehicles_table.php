<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('assigned_person_id')->nullable()->after('insurance_contract_global_id')->constrained('persons')->nullOnDelete();
            $table->string('assignment_type', 50)->nullable()->after('assigned_person_id')->comment('dotation, affectation, liaison, lucatelli, sec_surete, travaux, missions, transport_vip');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['assigned_person_id']);
            $table->dropColumn(['assigned_person_id', 'assignment_type']);
        });
    }
};
