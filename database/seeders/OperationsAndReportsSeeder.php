<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Driver;
use App\Models\Garage;
use App\Models\Mechanic;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class OperationsAndReportsSeeder extends Seeder
{
    public function run(): void
    {
        $garage = Garage::firstOrCreate(
            ['name' => 'Garage PAD Central'],
            ['type' => 'internal', 'is_active' => true]
        );

        $brand = Brand::firstOrCreate(
            ['name' => 'Toyota'],
            ['code' => 'TOYOT']
        );

        $models = [
            'Hilux',
            'Land Cruiser',
            'Corolla',
            'Prado',
            'Hiace',
        ];

        foreach ($models as $name) {
            VehicleModel::firstOrCreate(
                ['brand_id' => $brand->id, 'name' => $name],
                ['code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 8))]
            );
        }

        $vehicleModels = VehicleModel::where('brand_id', $brand->id)->get();
        if ($vehicleModels->isEmpty()) {
            return;
        }

        $drivers = Driver::query()->take(6)->get();
        $mechanics = Mechanic::query()->where('is_active', true)->take(4)->get();
        $articles = Article::query()->where('is_active', true)->take(25)->get();

        if ($drivers->isEmpty() || $mechanics->isEmpty() || $articles->isEmpty()) {
            return;
        }

        $registrations = [
            'LT-RPT-101',
            'LT-RPT-102',
            'LT-RPT-103',
            'LT-RPT-104',
            'LT-RPT-105',
            'LT-RPT-106',
        ];

        $vehicles = collect();
        foreach ($registrations as $index => $registration) {
            $model = $vehicleModels[$index % $vehicleModels->count()];
            $driver = $drivers[$index % $drivers->count()];

            $vehicles->push(Vehicle::updateOrCreate(
                ['registration' => $registration],
                [
                    'vehicle_model_id' => $model->id,
                    'category' => ['leger', '4x4', 'bus', 'camionnette'][$index % 4],
                    'purchase_date' => now()->subYears(2)->toDateString(),
                    'purchase_price' => 12000000 + ($index * 450000),
                    'venal_value' => 10000000 + ($index * 350000),
                    'mileage' => 10000 + ($index * 2500),
                    'status' => 'available',
                    'garage_id' => $garage->id,
                    'assigned_person_id' => null,
                    'assignment_type' => 'driver',
                    'notes' => 'Seed rapport automatique',
                ]
            ));
        }

        $periods = collect(range(1, 24)); // 24 interventions pour bien remplir hebdo/mensuel

        foreach ($periods as $i) {
            $vehicle = $vehicles[$i % $vehicles->count()];
            $mechanic = $mechanics[$i % $mechanics->count()];
            $start = now()->subDays($i * 3)->startOfDay()->addHours(8);
            $expected = $start->copy()->addDays(2 + ($i % 4));
            $completed = $start->copy()->addDays(1 + ($i % 5));

            $repair = Repair::create([
                'vehicle_id' => $vehicle->id,
                'garage_id' => $garage->id,
                'mechanic_id' => $mechanic->id,
                'type' => $i % 3 === 0 ? Repair::TYPE_EXTERNAL : Repair::TYPE_INTERNAL,
                'description' => 'Intervention planifiee seed #' . $i,
                'repair_type' => ['maintenance', 'panne', 'carrosserie'][$i % 3],
                'priority' => [Repair::PRIORITY_LOW, Repair::PRIORITY_MEDIUM, Repair::PRIORITY_HIGH, Repair::PRIORITY_URGENT][$i % 4],
                'estimated_duration' => 2 + ($i % 6),
                'cost' => 15000 + ($i * 2500),
                'started_at' => $start,
                'expected_completed_at' => $expected,
                'completed_at' => $completed,
                'quality_rating' => 3 + (($i + 1) % 3), // 3..5
                'delay_rating' => max(1, 5 - abs($expected->diffInDays($completed, false))), // meilleure note si proche du delai
                'evaluation_comment' => 'Evaluation seed: qualite et delai prestation.',
                'evaluated_at' => $completed->copy()->addHours(2),
                'notes' => 'SEED_REPORTS',
            ]);

            $partsCount = 1 + ($i % 3);
            for ($j = 0; $j < $partsCount; $j++) {
                $article = $articles[($i + $j) % $articles->count()];
                $qty = 1 + (($i + $j) % 4);
                $unit = (float) ($article->purchase_price ?? (1000 + (($i + $j) * 250)));

                RepairPart::create([
                    'repair_id' => $repair->id,
                    'article_id' => $article->id,
                    'quantity_used' => $qty,
                    'unit_price' => $unit,
                    'total_price' => $qty * $unit,
                    'stock_location' => ($j % 2 === 0) ? 'main' : 'garage',
                    'notes' => 'Consommation interne seed',
                ]);
            }
        }
    }
}

