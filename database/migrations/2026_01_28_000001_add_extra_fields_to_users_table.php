<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('matricule')->nullable()->unique()->after('id');
            $table->string('gender', 20)->nullable()->after('name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('occupation')->nullable()->after('phone');
            $table->timestamp('locked_until')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['matricule', 'gender', 'phone', 'occupation', 'locked_until']);
        });
    }
};
