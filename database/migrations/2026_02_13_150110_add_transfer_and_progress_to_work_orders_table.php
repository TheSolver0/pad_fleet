<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('transfer_reference')->nullable()->after('reference');
            $table->date('transfer_date')->nullable()->after('transfer_reference');
            $table->unsignedTinyInteger('completion_percent')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['transfer_reference', 'transfer_date', 'completion_percent']);
        });
    }
};
