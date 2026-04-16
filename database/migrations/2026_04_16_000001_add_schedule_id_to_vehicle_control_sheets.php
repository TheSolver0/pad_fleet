<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_control_sheets', function (Blueprint $table) {
            $table->foreignId('vehicle_schedule_id')
                ->nullable()
                ->after('mission_id')
                ->constrained('vehicle_schedules')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_control_sheets', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\VehicleSchedule::class);
            $table->dropColumn('vehicle_schedule_id');
        });
    }
};
