<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(DirectionsDepartmentsServicesSeeder::class);
        $this->call(ServicesSeeder::class);
        $this->call(BrandsAndVehicleModelsSeeder::class);
        $this->call(AssureursSeeder::class);
        
        $this->call(DriverSeeder::class);
        $this->call(DrivingLicenseSeeder::class);
        $this->call(PersonSeeder::class);
        
        // Stock and mechanics
        $this->call(ArticleCategorySeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(MechanicSeeder::class);
        $this->call(CitySeeder::class);

        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Kenny LOMIE',
                'matricule' => '12345',
                'gender' => 'M',
                'phone' => '+237 6XX XXX XXX',
                'occupation' => 'Administrateur',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        $user->syncRoles(['Administrateur']);
    }
}
