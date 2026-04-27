<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_control_sheets', function (Blueprint $table) {
            $table->string('signature_depart_path')->nullable()->after('observations_retour');
            $table->string('signature_retour_path')->nullable()->after('signature_depart_path');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_control_sheets', function (Blueprint $table) {
            $table->dropColumn(['signature_depart_path', 'signature_retour_path']);
        });
    }
};

