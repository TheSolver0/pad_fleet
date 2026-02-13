<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driving_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->string('license_type'); // A, B, C, D, E, etc.
            $table->string('category'); // A1, A2, B1, B, C1, C, D1, D, etc.
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('issuing_authority');
            $table->string('issuing_country', 100)->default('CM');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable(); // Scan du permis
            $table->timestamps();
            
            $table->index(['driver_id', 'is_active']);
            $table->index(['expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driving_licenses');
    }
};
