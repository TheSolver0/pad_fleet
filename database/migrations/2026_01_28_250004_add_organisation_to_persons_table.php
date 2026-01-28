<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->foreignId('direction_id')->nullable()->after('phone')->constrained('directions')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('direction_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('org_service_id')->nullable()->after('department_id')->constrained('org_services')->nullOnDelete();
        });
        if (Schema::hasColumn('persons', 'department')) {
            Schema::table('persons', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('department', 100)->nullable()->after('phone');
        });
        Schema::table('persons', function (Blueprint $table) {
            $table->dropForeign(['direction_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['org_service_id']);
        });
    }
};
