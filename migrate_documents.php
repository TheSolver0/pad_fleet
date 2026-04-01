<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    // Check if table exists
    if (!Schema::hasTable('mission_documents')) {
        Schema::create('mission_documents', function ($table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type'); // image, pdf
            $table->string('mime_type');
            $table->string('document_type')->nullable(); // ordre_mission, rapport, facture, etc.
            $table->string('caption')->nullable();
            $table->integer('file_size'); // en bytes
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        echo "Migration completed: mission_documents table created.\n";
    } else {
        echo "Table mission_documents already exists.\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}