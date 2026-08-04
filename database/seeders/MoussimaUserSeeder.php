<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MoussimaUserSeeder extends Seeder
{
    private const EMAIL = 'moussima2000@yahoo.fr';

    public function run(): void
    {
        $roleName = env('MOUSSIMA_ROLE') ?: 'Administrateur';

        if (! Role::where('name', $roleName)->where('guard_name', 'web')->exists()) {
            $this->call(RolesAndPermissionsSeeder::class);
        }

        $user = User::firstOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => env('MOUSSIMA_NAME') ?: 'Moussima',
                'occupation' => env('MOUSSIMA_OCCUPATION') ?: 'Administrateur',
                'password' => Hash::make(env('MOUSSIMA_PASSWORD') ?: 'password'),
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
