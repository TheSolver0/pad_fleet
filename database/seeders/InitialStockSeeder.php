<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InitialStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Ce seeder insère le stock initial basé sur les fiches de stock du garage DAG
     * Date de référence: 2025-01-28
     */
    public function run(): void
    {
        $stockMovements = [
            [
                'article_id' => 1, // Plaquette frein AV Hilux/Fortuner
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 2, // Plaquette frein AV Suzuki/RUNION
                'movement_type' => 'entry',
                'quantity' => 20,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 3, // Plaquette frein AR Fortuner
                'movement_type' => 'entry',
                'quantity' => 8,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 4, // Plaquette frein AR Fortuner
                'movement_type' => 'entry',
                'quantity' => 4,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 5, // Plaquette frein AR Fortuner
                'movement_type' => 'entry',
                'quantity' => 4,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 6, // Plaquette frein AV Mitsubishi
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 7, // Plaquette frein AV AVANZA
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 8, // Plaquette frein AV 3008 Speed Tel
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 9, // Plaquette frein AV 3008 BER
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 10, // Plaquette frein AR Corolla ASIMCO
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 11, // Plaquette frein AV Corolla ASIMCO
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 12, // Plaquette frein AV PRADO Hilux
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 13, // Plaquette frein AV PRADO-Hilux
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 14, // Plaquette frein AV CIVILIAN
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 15, // Plaquette frein AV Sous carton ASIMCO
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-03',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 03',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 16, // Garniture de frein AR Hilux
                'movement_type' => 'entry',
                'quantity' => 10,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 17, // Garniture de frein AR Hilux
                'movement_type' => 'entry',
                'quantity' => 7,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 18, // Compresseur ou pistolet à boullons pneumatique
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 19, // Disque d'embrayage Hilux
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 20, // Plateau d'embrayage Hilux
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 21, // Garniture de distribution Hilux courroies accessoi
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 22, // 
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 23, // Scie à métaux
                'movement_type' => 'entry',
                'quantity' => 3,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-04',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 04',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 24, // Compresseur d'air pour pneus
                'movement_type' => 'entry',
                'quantity' => 4,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 25, // Compresseur d'air pour pneus
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 26, // Siren ROCKY FE
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 27, // Klaxon HELLA
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 28, // Musicuse accordéon
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 29, // Pot de feu rouge AR Hilux Gauche
                'movement_type' => 'entry',
                'quantity' => 8,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 30, // Pot de feu rouge AR Hilux Droit
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 31, // Pot de phare AV Hilux Gauche
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 32, // Pot de phare AV Hilux droit
                'movement_type' => 'entry',
                'quantity' => 3,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 33, // Coffre à douilles HANS
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 34, // Coffret à compressiomètre
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 35, // Karcher K7 Premium Power
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 36, // Pince à soudure (Positif)
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 37, // Pince à soudure (Positif)
                'movement_type' => 'entry',
                'quantity' => 4,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 38, // Pince à soudure (Négatif)
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 39, // Pince à soudure (Négatif) masq
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 40, // Mini poste à soudure Boster
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 41, // Mini poste à soudure WISELIF
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 42, // Aspirateur de vidange
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-05',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 05',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 43, // Bache pour véhicule
                'movement_type' => 'entry',
                'quantity' => 8,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 44, // Alarmes Octopus Saga
                'movement_type' => 'entry',
                'quantity' => 20,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 45, // Alarme Octopus Saga
                'movement_type' => 'entry',
                'quantity' => 5,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 46, // Alarme Bravo
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 47, // Alarme ROYAL BEMAZ
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 48, // Alarme TOYOTA
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 49, // Pots de graisse MULTIS TOTAL 1Kg
                'movement_type' => 'entry',
                'quantity' => 16,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 50, // Mini Karcher Robot magic
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 51, // Mini Karcher
                'movement_type' => 'entry',
                'quantity' => 21,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 52, // Jante alu
                'movement_type' => 'entry',
                'quantity' => 4,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 53, // Flacon de 400ml Total Wash
                'movement_type' => 'entry',
                'quantity' => 11,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 54, // Aérosol décappant Total
                'movement_type' => 'entry',
                'quantity' => 5,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 55, // Bloc-volant de sécurité
                'movement_type' => 'entry',
                'quantity' => 8,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 56, // Bloc-volant CC Legend
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 57, // Bloc-volant Good Luck
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 58, // Pot de mastic National
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-07',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 07',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 59, // Paire de gant en caoutchouc
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 60, // Chariot porte-bouteille
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 61, // Bouteille d'oxygène
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 62, // Bouteille d'acétylène
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 63, // Anti-fuite radiateur Total flacon de 300ml
                'movement_type' => 'entry',
                'quantity' => 12,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 64, // Savon liquide pour vitre Sim Classe 750ml
                'movement_type' => 'entry',
                'quantity' => 15,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 65, // Etau TOTAL
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 66, // Disque avant TOYOTA COROLLA
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 67, // Disque ARRIERE Corolla
                'movement_type' => 'entry',
                'quantity' => 4,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 68, // Pistolet à peinture pneumatique Eang
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 69, // Sangle de remorquage 5m
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 70, // Rivet 6x40mm
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 71, // Rivet 6.4x40mm
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 72, // Rivet 3.2x 9.6mm
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 73, // Disque dure ordinateur Clavier
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 74, // 
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-08',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 08',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 75, // Balai d'essui glace Toyota moyen
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 76, // Balai d'essui glace Toyota court
                'movement_type' => 'entry',
                'quantity' => 6,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 77, // Amortisseur Civilian
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 78, // Feu Arrière gauche droite Coaster
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 79, // Clé à pipe et N° 30 27 24 21 19 18 16 15 13 16 9 8
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 80, // Clé à pans multiples 122-31
                'movement_type' => 'entry',
                'quantity' => 1,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 81, // Clé à pans multiples 122-22
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 82, // Clé mix à pans fermé N° 32 30 27 24 22 21
                'movement_type' => 'entry',
                'quantity' => 2,
                'unit_price' => null,
                'total_price' => null,
                'movement_date' => Carbon::parse('2025-01-28'),
                'document_reference' => 'STOCK-INIT-DAG-09',
                'supplier_id' => null,
                'warehouse_id' => 1, // Garage DAG N°1(A)
                'user_id' => 1,
                'notes' => 'Stock initial - Fiche page 09',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insérer les mouvements de stock
        // Note: Adaptez le nom de la table selon votre migration
        // DB::table('stock_movements')->insert($stockMovements);
        
        // OU si vous utilisez un modèle Article avec une colonne current_stock:
        foreach ($stockMovements as $movement) {
            DB::table('articles')
                ->where('id', $movement['article_id'])
                ->update(['current_stock' => $movement['quantity']]);
        }
    }
}