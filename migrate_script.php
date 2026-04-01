<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    // Check if table exists
    if (!Schema::hasTable('mission_photos')) {
        Schema::create('mission_photos', function ($table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->enum('type', ['before', 'after'])->default('before');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->date('taken_at')->nullable();
            $table->timestamps();
        });

        echo "Migration completed: mission_photos table created.\n";
    } else {
        echo "Table mission_photos already exists.\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}