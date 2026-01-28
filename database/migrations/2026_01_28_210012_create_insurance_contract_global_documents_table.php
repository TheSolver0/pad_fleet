<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_contract_global_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insurance_contract_global_id');
            $table->foreign('insurance_contract_global_id', 'icg_docs_contract_id_fk')
                ->references('id')->on('insurance_contract_globals')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_contract_global_documents');
    }
};
