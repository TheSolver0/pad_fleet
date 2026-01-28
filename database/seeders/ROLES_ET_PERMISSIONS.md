# Rôles et permissions — PAD Fleet

## Lancer le seeder

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
# ou pour tout reseeder (utilisateur + rôles)
php artisan db:seed --force
```

## Rôles et permissions associées

| Acteur | Rôle (name) | Permissions (slugs) |
|--------|-------------|----------------------|
| **Administrateur** | `Administrateur` | Toutes les permissions |
| **Chef Garage** | `Chef Garage` | `planification-maintenance`, `affectation-mecaniciens`, `validation-bons-sortie`, `ordres-travail` |
| **Mécanicien** | `Mécanicien` | `enregistrement-interventions`, `demande-pieces`, `comptes-rendus`, `photos-avant-apres` |
| **Magasinier** | `Magasinier` | `gestion-stock`, `reception-commandes`, `sorties-pieces`, `inventaires`, `alertes-stock` |
| **Gestionnaire Flotte** | `Gestionnaire Flotte` | `planning-missions`, `affectations-vehicules-chauffeurs`, `suivi-assurances`, `sinistres`, `rapports-flotte` |
| **Chauffeur** | `Chauffeur` | `consultation-missions`, `saisie-km`, `signalement-anomalies`, `carnets-bord` |
| **Directeur / Chef Service** | `Directeur / Chef Service` | `demandes-reservation`, `consultation-disponibilites`, `approbations`, `suivi-budget` |
| **Contrôleur Financier** | `Contrôleur Financier` | `rapports-financiers`, `tco`, `budgets`, `factures`, `analyses-couts`, `ecarts` |
| **Direction** | `Direction` | `tableaux-bord-strategiques`, `kpis`, `aide-decision`, `audits` |

## Utilisation dans le code

```php
// Vérifier un rôle
$user->hasRole('Administrateur');
$user->hasRole(['Chef Garage', 'Mécanicien']);

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
- demandes-reservation, consultation-disponibilites, approbations, suivi-budget
- rapports-financiers, tco, budgets, factures, analyses-couts, ecarts
- tableaux-bord-strategiques, kpis, aide-decision, audits
