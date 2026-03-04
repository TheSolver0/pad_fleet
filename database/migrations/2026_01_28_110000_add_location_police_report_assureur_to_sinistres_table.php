<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
            $table->string('police_report_path')->nullable()->after('notes');
            $table->foreignId('assureur_id')->nullable()->after('garage_id')->constrained('assureurs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->dropForeign(['assureur_id']);
            $table->dropColumn(['location', 'police_report_path', 'assureur_id']);
        });
    }
};
