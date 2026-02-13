<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider la table vehicles
        DB::table('vehicles')->delete();

        $vehicles = [
            [
                'registration' => 'LT 234 AB',
                'brand' => 'Toyota',
                'model' => 'Land Cruiser',
                'category' => Vehicle::CATEGORY_4X4,
                'assignment_type' => 'pool',
                'fuel_type' => 'Diesel',
                'engine_capacity' => 2700,
                'purchase_date' => '2022-01-15',
                'reform_year' => 2030,
                'color' => 'Blanc',
                'chassis_number' => 'JTEBU9JR501234567',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 567 CD',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'category' => Vehicle::CATEGORY_CAMIONNETTE,
                'assignment_type' => 'pool',
                'fuel_type' => 'Diesel',
                'engine_capacity' => 2400,
                'purchase_date' => '2022-03-20',
                'reform_year' => 2030,
                'color' => 'Gris',
                'chassis_number' => 'JTEBN9JR502345678',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 890 EF',
                'brand' => 'Toyota',
                'model' => 'Camry',
                'category' => Vehicle::CATEGORY_LEGER,
                'assignment_type' => 'pool',
                'fuel_type' => 'Essence',
                'engine_capacity' => 2000,
                'purchase_date' => '2021-06-10',
                'reform_year' => 2029,
                'color' => 'Noir',
                'chassis_number' => 'JTHBE5CR301234567',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 123 GH',
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'category' => Vehicle::CATEGORY_BUS,
                'assignment_type' => 'pool',
                'fuel_type' => 'Diesel',
                'engine_capacity' => 2700,
                'purchase_date' => '2021-09-15',
                'reform_year' => 2029,
                'color' => 'Blanc',
                'chassis_number' => 'JHTEBU9JR503456789',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 456 IJ',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'category' => Vehicle::CATEGORY_LEGER,
                'assignment_type' => 'pool',
                'fuel_type' => 'Essence',
                'engine_capacity' => 1800,
                'purchase_date' => '2022-11-25',
                'reform_year' => 2031,
                'color' => 'Argent',
                'chassis_number' => 'JTHBE5CR404567890',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 789 KL',
                'brand' => 'Toyota',
                'model' => 'Prado',
                'category' => Vehicle::CATEGORY_4X4,
                'assignment_type' => 'pool',
                'fuel_type' => 'Diesel',
                'engine_capacity' => 2700,
                'purchase_date' => '2020-04-08',
                'reform_year' => 2028,
                'color' => 'Bleu',
                'chassis_number' => 'JTEBU9JR505678901',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 321 MN',
                'brand' => 'Toyota',
                'model' => 'Yaris',
                'category' => Vehicle::CATEGORY_LEGER,
                'assignment_type' => 'pool',
                'fuel_type' => 'Essence',
                'engine_capacity' => 1500,
                'purchase_date' => '2023-02-14',
                'reform_year' => 2032,
                'color' => 'Rouge',
                'chassis_number' => 'JTHBE5CR506789012',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 654 OP',
                'brand' => 'Toyota',
                'model' => 'Land Cruiser',
                'category' => Vehicle::CATEGORY_4X4,
                'assignment_type' => 'pool',
                'fuel_type' => 'Diesel',
                'engine_capacity' => 4700,
                'purchase_date' => '2019-07-22',
                'reform_year' => 2027,
                'color' => 'Vert',
                'chassis_number' => 'JTEBU9JR607890123',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 987 QR',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'category' => Vehicle::CATEGORY_CAMIONNETTE,
                'assignment_type' => 'pool',
                'fuel_type' => 'Diesel',
                'engine_capacity' => 2400,
                'purchase_date' => '2021-12-05',
                'reform_year' => 2029,
                'color' => 'Blanc',
                'chassis_number' => 'JTEBN9JR608901234',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration' => 'LT 147 ST',
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'category' => Vehicle::CATEGORY_BUS,
                'assignment_type' => 'pool',
                'fuel_type' => 'Diesel',
                'engine_capacity' => 2700,
                'purchase_date' => '2020-10-18',
                'reform_year' => 2028,
                'color' => 'Gris',
                'chassis_number' => 'JHTEBU9JR609012345',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }

        $this->command->info('Véhicules créés avec succès!');
    }
}
