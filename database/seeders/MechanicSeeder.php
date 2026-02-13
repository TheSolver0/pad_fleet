<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MechanicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mechanics = [
            [
                'first_name' => 'Mampomo',
                'last_name' => 'Jeannot Sylvain',
                'matricule' => '',
                'phone' => null,
                'email' => null,
                'address' => null,
                'specialization' => 'Mécanicien',
                'hire_date' => Carbon::now()->subYears(3),
                'certificate' => null,
                // 'hourly_rate' => 2500.00?,
                'is_active' => true,
                'notes' => 'Mécanicien principal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'MEDJENANDJO',
                'last_name' => 'SAMA Claude Ferdinand',
                'matricule' => '',
                'phone' => null,
                'email' => null,
                'address' => null,
                'specialization' => 'tôlier auto ',
                'hire_date' => Carbon::now()->subYears(2),
                'certificate' => null,
                // 'hourly_rate' => 2200.00,
                'is_active' => true,
                'notes' => 'Spécialiste en diagnostics',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Tsimi',
                'last_name' => 'Roger',
                'matricule' => '6351-A',
                'phone' => null,
                'email' => null,
                'address' => null,
                'specialization' => 'Mécanicien',
                'hire_date' => Carbon::now()->subYears(4),
                'certificate' => '6351-A',
                // 'hourly_rate' => 2800.00,
                'is_active' => true,
                'notes' => 'Certification 6351-A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Okona Foto',
                'last_name' => 'Jean Blaise',
                'matricule' => '5926-G',
                'phone' => null,
                'email' => null,
                'address' => null,
                'specialization' => 'Mécanicien',
                'hire_date' => Carbon::now()->subYears(1),
                'certificate' => '5926-G',
                // 'hourly_rate' => 2000.00,
                'is_active' => true,
                'notes' => 'Certification 5926-G',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('mechanics')->insert($mechanics);
    }
}
