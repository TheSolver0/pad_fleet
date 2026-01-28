<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_contract_globals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('insurer');
            $table->string('lot_description')->nullable(); // ex: véhicules légers + motos
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('optional_prime', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_contract_globals');
    }
};
