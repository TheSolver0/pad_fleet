<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed de production : référentiels applicatifs, données PAD officielles, compte admin.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(DirectionsDepartmentsServicesSeeder::class);
        $this->call(ServicesSeeder::class);
        $this->call(BrandsAndVehicleModelsSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(CitySeeder::class);

        $this->call(DriverSeeder::class);
        $this->call(DrivingLicenseSeeder::class);
        $this->call(VehicleSeeder::class);

        $this->call(AdminUserSeeder::class);
    }
}
