<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostics', function (Blueprint $table) {
            $table->foreignId('garage_id')->nullable()->after('vehicle_id')->constrained()->nullOnDelete();
            $table->string('requester_type')->nullable()->after('user_role'); // driver|person
            $table->unsignedBigInteger('requester_id')->nullable()->after('requester_type');
            $table->index(['requester_type', 'requester_id']);
        });
    }

    public function down(): void
    {
        Schema::table('diagnostics', function (Blueprint $table) {
            $table->dropForeign(['garage_id']);
            $table->dropIndex(['requester_type', 'requester_id']);
            $table->dropColumn(['garage_id', 'requester_type', 'requester_id']);
        });
    }
};
