# Rôles et permissions — PAD Fleet

## Lancer le seeder

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
# ou seed de production (référentiels + données PAD + admin)
php artisan db:seed --force
```

En local uniquement, pour des données de démo (stock, mécaniciens, rapports fictifs) :

```bash
php artisan db:seed --class=DevelopmentSeeder
```

## Rôles et permissions associées

| Acteur | Rôle (name) | Permissions (slugs) |
|--------|-------------|----------------------|
| **Administrateur** | `Administrateur` | Toutes les permissions |
| **Chef Service** (ex Chef Garage) | `Chef Service` | `planification-maintenance`, `affectation-mecaniciens`, `validation-bons-sortie`, `ordres-travail` |
| **Mécanicien** | `Mécanicien` | `enregistrement-interventions`, `demande-pieces`, `comptes-rendus`, `photos-avant-apres` |
| **Magasinier** | `Magasinier` | `gestion-stock`, `reception-commandes`, `sorties-pieces`, `inventaires`, `alertes-stock` |
| **Gestionnaire Flotte** | `Gestionnaire Flotte` | `planning-missions`, `affectations-vehicules-chauffeurs`, `suivi-assurances`, `sinistres`, `rapports-flotte` |
| **Chauffeur** | `Chauffeur` | `consultation-missions`, `saisie-km`, `signalement-anomalies`, `carnets-bord` |
| **Chef Bureau Chauffeurs** | `Chef Bureau Chauffeurs` | `consultation-missions`, `comptes-rendus`, `gestion-chauffeurs-bureau` — accès restreint : consultation du planning, saisie du compte-rendu de mission, gestion de la liste des chauffeurs uniquement |
| **Chef Département** (ex Directeur / Chef Service) | `Chef Département` | `demandes-reservation`, `consultation-disponibilites`, `approbations`, `suivi-budget` |
| **Contrôleur Financier** | `Contrôleur Financier` | `rapports-financiers`, `tco`, `budgets`, `factures`, `analyses-couts`, `ecarts` |
| **Direction** | `Direction` | `tableaux-bord-strategiques`, `kpis`, `aide-decision`, `audits` |

## Utilisation dans le code

```php
// Vérifier un rôle
$user->hasRole('Administrateur');
$user->hasRole(['Chef Service', 'Mécanicien']);

// Vérifier une permission
$user->can('gestion-stock');
$user->hasPermissionTo('validation-bons-sortie');

// Via middleware (à enregistrer si besoin)
// $user->assignRole('Chauffeur');
// $user->syncRoles(['Gestionnaire Flotte']);
```

## Liste de toutes les permissions (slugs)

- parametrage-systeme, gestion-utilisateurs, exports-massifs, archives
- planification-maintenance, affectation-mecaniciens, validation-bons-sortie, ordres-travail
- enregistrement-interventions, demande-pieces, comptes-rendus, photos-avant-apres
- gestion-stock, reception-commandes, sorties-pieces, inventaires, alertes-stock
- planning-missions, affectations-vehicules-chauffeurs, suivi-assurances, sinistres, rapports-flotte
- consultation-missions, saisie-km, signalement-anomalies, carnets-bord
- gestion-chauffeurs-bureau
- demandes-reservation, consultation-disponibilites, approbations, suivi-budget
- rapports-financiers, tco, budgets, factures, analyses-couts, ecarts
- tableaux-bord-strategiques, kpis, aide-decision, audits
