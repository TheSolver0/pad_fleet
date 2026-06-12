<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', app()->environment('local') ? 'test@example.com' : null);

        if (! $email) {
            $this->command?->warn('ADMIN_EMAIL non défini : aucun compte administrateur créé.');

            return;
        }

        if (app()->isProduction() && ! env('ADMIN_PASSWORD')) {
            $this->command?->warn('ADMIN_PASSWORD requis en production : aucun compte administrateur créé.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Administrateur PAD'),
                'matricule' => env('ADMIN_MATRICULE'),
                'gender' => env('ADMIN_GENDER', 'M'),
                'phone' => env('ADMIN_PHONE'),
                'occupation' => env('ADMIN_OCCUPATION', 'Administrateur'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ]
        );

        $user->syncRoles(['Administrateur']);

        $this->command?->info("Compte administrateur prêt : {$email}");
    }
}
