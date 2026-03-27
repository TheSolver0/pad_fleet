<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_inspections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

            $table->date('inspected_at');
            $table->date('expires_at');

            $table->enum('result', ['admitted', 'adjourned', 'refused'])->default('admitted');

            $table->string('control_center')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('certificate_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_inspections');
    }
};