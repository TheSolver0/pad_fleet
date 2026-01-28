<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class BrandsAndVehicleModelsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Toyota' => ['Corolla', 'Hilux', 'Land Cruiser', 'RAV4', 'Yaris', 'Camry', 'Hiace', 'Prado'],
            'Peugeot' => ['208', '301', '308', '508', '2008', '3008', '5008', 'Partner', 'Boxer'],
            'Renault' => ['Clio', 'Logan', 'Sandero', 'Duster', 'Kadjar', 'Captur', 'Kangoo', 'Master', 'Trafic'],
            'Nissan' => ['Micra', 'Qashqai', 'X-Trail', 'Navara', 'Patrol', 'Juke', 'Leaf', 'NV200'],
            'Hyundai' => ['i20', 'i30', 'Tucson', 'Santa Fe', 'Kona', 'Creta', 'Starex', 'Porter'],
            'Kia' => ['Picanto', 'Rio', 'Cerato', 'Sportage', 'Sorento', 'Seltos', 'Carens', 'Bongo'],
            'Mercedes-Benz' => ['Classe A', 'Classe C', 'Classe E', 'Classe S', 'Vito', 'Sprinter', 'GLA', 'GLC'],
            'BMW' => ['Série 1', 'Série 3', 'Série 5', 'X1', 'X3', 'X5', 'Série 2 Active Tourer'],
            'Volkswagen' => ['Polo', 'Golf', 'Passat', 'Tiguan', 'T-Roc', 'Caddy', 'Transporter', 'Amarok'],
            'Ford' => ['Fiesta', 'Focus', 'Mondeo', 'Puma', 'Kuga', 'Ranger', 'Transit', 'Tourneo'],
            'Mitsubishi' => ['ASX', 'Outlander', 'Pajero', 'L200', 'Eclipse Cross', 'Space Star'],
            'Suzuki' => ['Swift', 'Baleno', 'Vitara', 'Jimny', 'S-Cross', 'Ertiga', 'Alto', 'APV'],
            'Isuzu' => ['D-Max', 'MU-X', 'D-Max Hi-Lander', 'FRR', 'FSR', 'FVR'],
            'Chevrolet' => ['Spark', 'Aveo', 'Captiva', 'Trailblazer', 'Colorado', 'N300'],
            'Fiat' => ['Punto', 'Tipo', '500', 'Panda', 'Ducato', 'Doblo', 'Strada'],
            'Dacia' => ['Sandero', 'Logan', 'Duster', 'Spring', 'Jogger', 'Dokker'],
            'Honda' => ['Jazz', 'Civic', 'Accord', 'HR-V', 'CR-V', 'BR-V', 'City'],
            'Mazda' => ['2', '3', '6', 'CX-3', 'CX-5', 'CX-30', 'BT-50'],
            'Land Rover' => ['Defender', 'Discovery', 'Range Rover', 'Range Rover Evoque', 'Range Rover Sport'],
            'Jeep' => ['Renegade', 'Compass', 'Cherokee', 'Wrangler', 'Grand Cherokee'],
            'Yamaha' => ['XTZ 125', 'YBR 125', 'FZ-S', 'MT-07', 'Ténéré 700'],
            'Honda Moto' => ['CB 125', 'CBR 150', 'Africa Twin', 'PCX', 'SH 300'],
            'Piaggio' => ['Vespa', 'Liberty', 'Beverly', 'MP3', 'Porter'],
        ];

        // Codes explicites pour éviter les doublons (ex: Honda vs Honda Moto -> HONDA)
        $codes = [
            'Honda Moto' => 'HONDM',
            'Land Rover' => 'LNDRV',
            'Mercedes-Benz' => 'MERCE',
        ];

        foreach ($data as $brandName => $models) {
            $code = $codes[$brandName] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $brandName), 0, 5));
            $brand = Brand::firstOrCreate(
                ['name' => $brandName],
                ['code' => $code]
            );
            foreach ($models as $modelName) {
                VehicleModel::firstOrCreate(
                    [
                        'brand_id' => $brand->id,
                        'name' => $modelName,
                    ],
                    ['code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $modelName), 0, 10))]
                );
            }
        }
    }
}
