<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AnicetUserSeeder extends Seeder
{
    private const EMAIL = 'anicetsinndo@yahoo.com';

    public function run(): void
    {
        $roleName = env('ANICET_ROLE') ?: 'Gestionnaire Flotte';

        if (! Role::where('name', $roleName)->where('guard_name', 'web')->exists()) {
            $this->call(RolesAndPermissionsSeeder::class);
        }

        $user = User::firstOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => env('ANICET_NAME') ?: 'Anicet Sinndo',
                'occupation' => env('ANICET_OCCUPATION') ?: 'Chef Bureau Chauffeur',
                'password' => Hash::make(env('ANICET_PASSWORD') ?: 'password'),
                'is_active' => true,
            ],
        );

        if (! $user->hasRole($roleName)) {
            $user->assignRole($roleName);
        }

        $status = $user->wasRecentlyCreated ? 'créé' : 'déjà existant, conservé';
        $this->command?->info("Compte {$status} : ".self::EMAIL);
    }
}
