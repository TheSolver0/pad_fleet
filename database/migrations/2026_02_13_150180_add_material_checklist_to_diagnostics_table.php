<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostics', function (Blueprint $table) {
            $table->boolean('has_admin_file')->default(false)->after('km_arrival');
            $table->boolean('has_jack')->default(false)->after('has_admin_file');
            $table->boolean('has_wheel_key')->default(false)->after('has_jack');
            $table->boolean('has_spare_wheel')->default(false)->after('has_wheel_key');
            $table->boolean('has_first_aid')->default(false)->after('has_spare_wheel');
        });
    }

    public function down(): void
    {
        Schema::table('diagnostics', function (Blueprint $table) {
            $table->dropColumn(['has_admin_file', 'has_jack', 'has_wheel_key', 'has_spare_wheel', 'has_first_aid']);
        });
    }
};
