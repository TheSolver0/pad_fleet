<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== VÉRIFICATION DE LA BASE DE DONNÉES ===\n\n";

$tables = ['missions', 'mission_photos', 'mission_documents', 'cities', 'vehicles', 'drivers', 'demandeurs'];
echo "Vérification des tables :\n";
foreach($tables as $table) {
    $exists = Schema::hasTable($table);
    echo "- " . $table . ": " . ($exists ? "✓ Existe" : "✗ Manquante") . "\n";
}

echo "\nVérification des colonnes :\n";
if (Schema::hasTable('missions')) {
    $columns = Schema::getColumnListing('missions');
    $required = ['city_id', 'destination'];
    foreach($required as $col) {
        echo "- missions." . $col . ": " . (in_array($col, $columns) ? "✓ Existe" : "✗ Manquante") . "\n";
    }
}

if (Schema::hasTable('mission_photos')) {
    $columns = Schema::getColumnListing('mission_photos');
    $required = ['mission_id', 'file_path', 'type'];
    foreach($required as $col) {
        echo "- mission_photos." . $col . ": " . (in_array($col, $columns) ? "✓ Existe" : "✗ Manquante") . "\n";
    }
}

if (Schema::hasTable('mission_documents')) {
    $columns = Schema::getColumnListing('mission_documents');
    $required = ['mission_id', 'file_path', 'file_type', 'document_type'];
    foreach($required as $col) {
        echo "- mission_documents." . $col . ": " . (in_array($col, $columns) ? "✓ Existe" : "✗ Manquante") . "\n";
    }
}

echo "\n=== VÉRIFICATION DES MODÈLES ===\n\n";

$models = [
    'App\Models\Mission',
    'App\Models\MissionPhoto',
    'App\Models\MissionDocument',
    'App\Models\City',
    'App\Models\Vehicle',
    'App\Models\Driver',
    'App\Models\Demandeur'
];

foreach($models as $model) {
    try {
        $instance = new $model();
        echo "- " . $model . ": ✓ OK\n";
    } catch (Exception $e) {
        echo "- " . $model . ": ✗ Erreur - " . $e->getMessage() . "\n";
    }
}

echo "\n=== VÉRIFICATION TERMINÉE ===\n";