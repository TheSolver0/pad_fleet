<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('vehicle_model_id')->nullable()->after('id')->constrained('vehicle_models')->nullOnDelete();
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['brand', 'model']);
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('brand')->after('registration');
            $table->string('model')->after('brand');
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['vehicle_model_id']);
        });
    }
};
