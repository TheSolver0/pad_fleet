<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Données de démonstration / développement uniquement.
 * Ne pas exécuter en production.
 */
class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AssureursSeeder::class);
        $this->call(PersonSeeder::class);
        $this->call(ArticleCategorySeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(MechanicSeeder::class);
        $this->call(OperationsAndReportsSeeder::class);
        $this->call(Fictive\FictiveReportsSeeder::class);
    }
}
