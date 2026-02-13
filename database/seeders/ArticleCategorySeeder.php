<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Plaquettes de frein',
                'code' => 'PF',
                'description' => 'Plaquettes de frein avant et arrière pour différents véhicules',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Garnitures de frein',
                'code' => 'GF',
                'description' => 'Garnitures de frein arrière',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pièces d\'embrayage',
                'code' => 'PE',
                'description' => 'Disques et plateaux d\'embrayage',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Outils pneumatiques',
                'code' => 'OP',
                'description' => 'Compresseurs, pistolets, outils à air comprimé',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Optiques et éclairage',
                'code' => 'OE',
                'description' => 'Phares, feux arrière, ampoules',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Systèmes d\'alarme',
                'code' => 'SA',
                'description' => 'Alarmes antivol pour véhicules',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Lubrifiants et fluides',
                'code' => 'LF',
                'description' => 'Graisses, huiles, liquides de frein, etc.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Outillage main',
                'code' => 'OM',
                'description' => 'Clés, coffrets de douilles, pinces, scies',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Équipement de nettoyage',
                'code' => 'EN',
                'description' => 'Karcher, aspirateurs, produits de nettoyage',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Équipement de soudure',
                'code' => 'ES',
                'description' => 'Postes de soudure, pinces, accessoires',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Accessoires véhicule',
                'code' => 'AV',
                'description' => 'Bâches, blocs-volants, pare-soleil, etc.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Essuie-glaces',
                'code' => 'EG',
                'description' => 'Balais d\'essuie-glace',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Amortisseurs',
                'code' => 'AM',
                'description' => 'Amortisseurs pour différents véhicules',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Disques de frein',
                'code' => 'DF',
                'description' => 'Disques de frein avant et arrière',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Équipement de garage',
                'code' => 'EGa',
                'description' => 'Étaux, chariots, équipements divers',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Peinture et finition',
                'code' => 'PFi',
                'description' => 'Pistolets à peinture, mastics, aérosols',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jantes et roues',
                'code' => 'JR',
                'description' => 'Jantes aluminium et accessoires',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Équipement de remorquage',
                'code' => 'ER',
                'description' => 'Sangles, crochets, accessoires de remorquage',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Quincaillerie',
                'code' => 'QI',
                'description' => 'Rivets, boulons, fixations diverses',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Équipement de protection',
                'code' => 'EP',
                'description' => 'Gants, équipements de sécurité',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('article_categories')->insert($categories);
    }
}