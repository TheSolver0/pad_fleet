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
        Schema::create('repair_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->onDelete('cascade');
            $table->string('document_type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_filename');
            $table->integer('file_size');
            $table->string('mime_type');
            
            // Validation physique
            $table->boolean('physically_validated')->default(false);
            $table->foreignId('physically_validated_by')->nullable()->constrained('users');
            $table->timestamp('physically_validated_at')->nullable();
            
            // Validation digitale
            $table->boolean('digitally_validated')->default(false);
            $table->foreignId('digitally_validated_by')->nullable()->constrained('users');
            $table->timestamp('digitally_validated_at')->nullable();
            
            $table->text('validation_notes')->nullable();
            $table->timestamps();
            
            $table->index(['repair_id', 'document_type']);
            $table->index(['physically_validated', 'digitally_validated']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_documents');
    }
};
