<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->string('type', 50)->default('conge_annuel');
            // conge_annuel | maladie | permission | mission_officielle | autre
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('nb_jours')->nullable();
            $table->string('reason', 500)->nullable();
            $table->string('status', 30)->default('pending'); // pending | approved | rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('replacement_driver_id')->nullable(); // suggestion remplaçant
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable(); // justificatif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_leaves');
    }
};
