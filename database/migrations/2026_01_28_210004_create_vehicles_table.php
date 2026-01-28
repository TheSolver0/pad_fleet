<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration', 20)->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('category', 50)->nullable(); // léger, utilitaire, moto, etc.
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('venal_value', 12, 2)->nullable(); // calculé ou saisi
            $table->unsignedBigInteger('mileage')->default(0);
            $table->string('status', 30)->default('available'); // available, in_use, repair, out_of_service
            $table->foreignId('garage_id')->nullable()->constrained('garages')->nullOnDelete();
            $table->foreignId('insurance_contract_global_id')->nullable()->constrained('insurance_contract_globals')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
