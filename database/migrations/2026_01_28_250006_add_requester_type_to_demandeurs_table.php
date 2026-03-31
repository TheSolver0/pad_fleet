<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandeurs', function (Blueprint $table) {
            $table->string('demandeur_type', 20)->default('person')->after('name'); // person|direction
            $table->foreignId('person_id')->nullable()->after('service_id')->constrained('persons')->nullOnDelete();
            $table->foreignId('direction_id')->nullable()->after('person_id')->constrained('directions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('demandeurs', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropForeign(['direction_id']);
            $table->dropColumn(['demandeur_type', 'person_id', 'direction_id']);
        });
    }
};
