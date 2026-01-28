<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->date('assignment_start_at')->nullable()->after('assignment_type');
            $table->date('assignment_end_at')->nullable()->after('assignment_start_at')->comment('Null = période indéfinie');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['assignment_start_at', 'assignment_end_at']);
        });
    }
};
