<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersPerRoleSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'       => 'Admin PAD',
                'email'      => 'admin@pad.local',
                'matricule'  => 'PAD-ADM-001',
                'gender'     => 'M',
                'phone'      => '600000001',
                'occupation' => 'Administrateur système',
                'role'       => 'Administrateur',
            ],
            [
                'name'       => 'Kenny Lomie',
                'email'      => 'klomie@pad.local',
                'matricule'  => 'PAD-ADM-002',
                'gender'     => 'M',
                'phone'      => '600000010',
                'occupation' => 'Administrateur système',
                'role'       => 'Administrateur',
            ],
            [
                'name'       => 'Jean-Pierre Kamga',
                'email'      => 'chef.garage@pad.local',
                'matricule'  => 'PAD-CGG-001',
                'gender'     => 'M',
                'phone'      => '600000002',
                'occupation' => 'Chef Service',
                'role'       => 'Chef Service',
            ],
            [
                'name'       => 'Brice Nkoulou',
                'email'      => 'mecanicien@pad.local',
                'matricule'  => 'PAD-MEC-001',
                'gender'     => 'M',
                'phone'      => '600000003',
                'occupation' => 'Mécanicien',
                'role'       => 'Mécanicien',
            ],
            [
                'name'       => 'Sandrine Eto',
                'email'      => 'magasinier@pad.local',
                'matricule'  => 'PAD-MAG-001',
                'gender'     => 'F',
                'phone'      => '600000004',
                'occupation' => 'Magasinière',
                'role'       => 'Magasinier',
            ],
            [
                'name'       => 'Paul Essomba',
                'email'      => 'gestionnaire.flotte@pad.local',
                'matricule'  => 'PAD-GFL-001',
                'gender'     => 'M',
                'phone'      => '600000005',
                'occupation' => 'Gestionnaire de Flotte',
                'role'       => 'Gestionnaire Flotte',
            ],
            [
                'name'       => 'Alain Mbarga',
                'email'      => 'chauffeur@pad.local',
                'matricule'  => 'PAD-CHF-001',
                'gender'     => 'M',
                'phone'      => '600000006',
                'occupation' => 'Chauffeur',
                'role'       => 'Chauffeur',
            ],
            [
                'name'       => 'Solange Ateba',
                'email'      => 'chef.bureau.chauffeurs@pad.local',
                'matricule'  => 'PAD-CBC-001',
                'gender'     => 'F',
                'phone'      => '600000010',
                'occupation' => 'Chef Bureau Chauffeurs',
                'role'       => 'Chef Bureau Chauffeurs',
            ],
            [
                'name'       => 'Isabelle Fouda',
                'email'      => 'directeur@pad.local',
                'matricule'  => 'PAD-DIR-001',
                'gender'     => 'F',
                'phone'      => '600000007',
                'occupation' => 'Cheffe de Département',
                'role'       => 'Chef Département',
            ],
            [
                'name'       => 'Narcisse Bello',
                'email'      => 'controleur.financier@pad.local',
                'matricule'  => 'PAD-CTF-001',
                'gender'     => 'M',
                'phone'      => '600000008',
                'occupation' => 'Contrôleur Financier',
                'role'       => 'Contrôleur Financier',
            ],
            [
                'name'       => 'Marie-Claire Ondoua',
                'email'      => 'direction@pad.local',
                'matricule'  => 'PAD-DRG-001',
                'gender'     => 'F',
                'phone'      => '600000009',
                'occupation' => 'Directrice Générale',
                'role'       => 'Direction',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')])
            );

            $user->syncRoles([$role]);

            $this->command?->info("  [{$role}] {$user->name} — {$user->email}");
        }
    }
}
