<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $persons = [
            [
                'name' => 'Chef de division',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => null,
                'notes' => 'Division de l\'Analyse, de la Prospective et de la Coopération',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Directeur Général',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => null,
                'notes' => 'Direction Générale',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Communication et Relations Publiques',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'MOUSSOM ANDRE BERTRAND',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Chef de division',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'Division du Suivi et de la Relance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Direction de la Capitainerie',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'NYEANCHI JOHN WIRBA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Direction des Ressources Humaines',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'NYOLLO NGOMA JOSEPH',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Service',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'WANDJI ATANGANA EUGENE STANISLAS',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Direction des Affaires Administratives et Financières',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'KOUEMO JEAN PAUL',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Direction de la Douala',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'FOTSO TAKOUTSING FRANCOIS XAVIER',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Direction de l\'Administration Centrale',
                'email' => '',
                'phone' => '+237 6 XX XX XX XX',
                // 'department' => '',
                'notes' => 'MVOGO JULES EMMANUEL',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('persons')->insert($persons);
    }
}