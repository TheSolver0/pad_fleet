<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('id_document_recto_path')->nullable()->after('notes');
            $table->string('id_document_verso_path')->nullable()->after('id_document_recto_path');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['id_document_recto_path', 'id_document_verso_path']);
        });
    }
};
