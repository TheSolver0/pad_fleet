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
        Schema::table('repairs', function (Blueprint $table) {
            $table->string('repair_type')->after('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('repair_type');
            $table->string('estimated_duration')->nullable()->after('priority');
            
            $table->index('repair_type');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropColumn(['repair_type', 'priority', 'estimated_duration']);
        });
    }
};
