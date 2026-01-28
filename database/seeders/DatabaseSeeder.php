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

        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Abdou Diouf',
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
