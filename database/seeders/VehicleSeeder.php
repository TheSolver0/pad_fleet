<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Database\Seeders\Support\VehicleParkParser;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = VehicleParkParser::parse();
        $imported = 0;

        foreach ($vehicles as $vehicleData) {
            $resolved = VehicleParkParser::resolveBrandAndModel($vehicleData['model_label']);
            $vehicleModelId = $this->resolveVehicleModelId($resolved['brand'], $resolved['model']);

            Vehicle::updateOrCreate(
                ['registration' => $vehicleData['registration']],
                [
                    'vehicle_model_id' => $vehicleModelId,
                    'category' => $vehicleData['category'],
                    'purchase_date' => $vehicleData['purchase_date'],
                    'purchase_price' => $vehicleData['purchase_price'],
                    'power' => $vehicleData['power'],
                    'status' => $vehicleData['status'],
                    'assignment_type' => $vehicleData['assignment_type'],
                    'notes' => trim(implode(' | ', array_filter([
                        $vehicleData['notes'],
                        $vehicleData['chassis_number'] ? 'Châssis: '.$vehicleData['chassis_number'] : null,
                    ]))) ?: null,
                ]
            );

            $imported++;
        }

        $this->command?->info("{$imported} véhicules importés depuis le parc automobile PAD.");
    }

    private function resolveVehicleModelId(string $brandName, string $modelName): int
    {
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $brandName), 0, 5));
        $brand = Brand::firstOrCreate(['name' => $brandName], ['code' => $code ?: 'AUTRE']);

        $vehicleModel = VehicleModel::firstOrCreate(
            [
                'brand_id' => $brand->id,
                'name' => $modelName,
            ],
            [
                'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $modelName), 0, 10)) ?: 'MODELE',
            ]
        );

        return $vehicleModel->id;
    }
}
