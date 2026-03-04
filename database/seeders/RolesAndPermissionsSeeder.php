<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    private const GUARD = 'web';

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = $this->createPermissions();
        $roles = $this->createRoles($permissions);

        $this->assignPermissionsToRoles($roles, $permissions);
    }

    private function createPermissions(): array
    {
        $list = [
            // Administrateur
            'parametrage-systeme',
            'gestion-utilisateurs',
            'exports-massifs',
            'archives',
            // Chef Garage
            'planification-maintenance',
            'affectation-mecaniciens',
            'validation-bons-sortie',
            'ordres-travail',
            // Mécanicien
            'enregistrement-interventions',
            'demande-pieces',
            'comptes-rendus',
            'photos-avant-apres',
            // Magasinier / Stock — entrées et sorties séparées (celui qui fait les entrées n'est pas nécessairement celui qui fait les sorties)
            'gestion-stock',
            'entrees-stock',
            'sorties-stock',
            'reception-commandes',
            'sorties-pieces',
            'inventaires',
            'alertes-stock',
            // Gestionnaire Flotte
            'planning-missions',
            'affectations-vehicules-chauffeurs',
            'suivi-assurances',
            'sinistres',
            'rapports-flotte',
            // Chauffeur
            'consultation-missions',
            'saisie-km',
            'signalement-anomalies',
            'carnets-bord',
            // Directeur / Chef Service
            'demandes-reservation',
            'consultation-disponibilites',
            'approbations',
            'suivi-budget',
            // Contrôleur Financier
            'rapports-financiers',
            'tco',
            'budgets',
            'factures',
            'analyses-couts',
            'ecarts',
            // Direction
            'tableaux-bord-strategiques',
            'kpis',
            'aide-decision',
            'audits',
        ];

        $out = [];
        foreach ($list as $name) {
            $out[$name] = Permission::firstOrCreate(['name' => $name, 'guard_name' => self::GUARD]);
        }
        return $out;
    }

    private function createRoles(array $permissions): array
    {
        $names = [
            'Administrateur',
            'Chef Garage',
            'Mécanicien',
            'Magasinier',
            'Gestionnaire Flotte',
            'Chauffeur',
            'Directeur / Chef Service',
            'Contrôleur Financier',
            'Direction',
        ];
        $out = [];
        foreach ($names as $name) {
            $out[$name] = Role::firstOrCreate(['name' => $name, 'guard_name' => self::GUARD]);
        }
        return $out;
    }

    private function assignPermissionsToRoles(array $roles, array $permissions): void
    {
        // Administrateur — Tous droits
        $roles['Administrateur']->syncPermissions(array_values($permissions));

        // Chef Garage
        $roles['Chef Garage']->syncPermissions([
            $permissions['planification-maintenance'],
            $permissions['affectation-mecaniciens'],
            $permissions['validation-bons-sortie'],
            $permissions['ordres-travail'],
        ]);

        // Mécanicien
        $roles['Mécanicien']->syncPermissions([
            $permissions['enregistrement-interventions'],
            $permissions['demande-pieces'],
            $permissions['comptes-rendus'],
            $permissions['photos-avant-apres'],
        ]);

        // Magasinier — peut avoir uniquement entrées, uniquement sorties, ou les deux selon affectation
        $roles['Magasinier']->syncPermissions([
            $permissions['gestion-stock'],
            $permissions['entrees-stock'],
            $permissions['sorties-stock'],
            $permissions['reception-commandes'],
            $permissions['sorties-pieces'],
            $permissions['inventaires'],
            $permissions['alertes-stock'],
        ]);

        // Gestionnaire Flotte
        $roles['Gestionnaire Flotte']->syncPermissions([
            $permissions['planning-missions'],
            $permissions['affectations-vehicules-chauffeurs'],
            $permissions['suivi-assurances'],
            $permissions['sinistres'],
            $permissions['rapports-flotte'],
        ]);

        // Chauffeur
        $roles['Chauffeur']->syncPermissions([
            $permissions['consultation-missions'],
            $permissions['saisie-km'],
            $permissions['signalement-anomalies'],
            $permissions['carnets-bord'],
        ]);

        // Directeur / Chef Service
        $roles['Directeur / Chef Service']->syncPermissions([
            $permissions['demandes-reservation'],
            $permissions['consultation-disponibilites'],
            $permissions['approbations'],
            $permissions['suivi-budget'],
        ]);

        // Contrôleur Financier
        $roles['Contrôleur Financier']->syncPermissions([
            $permissions['rapports-financiers'],
            $permissions['tco'],
            $permissions['budgets'],
            $permissions['factures'],
            $permissions['analyses-couts'],
            $permissions['ecarts'],
        ]);

        // Direction
        $roles['Direction']->syncPermissions([
            $permissions['tableaux-bord-strategiques'],
            $permissions['kpis'],
            $permissions['aide-decision'],
            $permissions['audits'],
        ]);
    }
}
