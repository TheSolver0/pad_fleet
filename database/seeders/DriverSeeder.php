<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drivers = [
            [
                'user_id' => null,
                'matricule' => '5350-S',
                'first_name' => 'NICOLAS DEMYRE',
                'last_name' => 'AYISSI',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'ayissi.nicolas@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Chef de la Division de l\'Analyse, de la Prospective et de la Coopération',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '5445-T',
                'first_name' => 'MENYE ALBERT SERGE',
                'last_name' => 'ETOMO',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'etomo.albert@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Directeur Général',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '5005-N',
                'first_name' => 'ANDRE BERTRAND',
                'last_name' => 'MOUSSOM',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'moussom.andre@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Communication et des Relations Publiques',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '4383-V',
                'first_name' => 'MENGOUMOU AURELIEN BIENVENU',
                'last_name' => 'TOKEA',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'tokea.aurelien@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Chef de la Division du Suivi et de la Relance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '4722-B',
                'first_name' => 'JOHN WIRBA',
                'last_name' => 'NYEANCHI',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'nyeanchi.john@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Direction de la Capitainerie',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '4925-Y',
                'first_name' => 'NGOMA JOSEPH',
                'last_name' => 'NYOLLO',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'nyollo.joseph@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Direction des Ressources Humaines',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '4918-R',
                'first_name' => 'ATANGANA EUGENE STANISLAS',
                'last_name' => 'WANDJI',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'wandji.eugene@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '3976-H',
                'first_name' => 'JEAN PAUL',
                'last_name' => 'KOUEMO',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'kouemo.jeanpaul@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Direction des Affaires Administratives et Financières',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '3835-E',
                'first_name' => 'TAKOUTSING FRANCOIS XAVIER',
                'last_name' => 'FOTSO',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'fotso.francois@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Direction de la Douala',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null,
                'matricule' => '3655-Z',
                'first_name' => 'JULES EMMANUEL',
                'last_name' => 'MVOGO',
                'phone' => '+237 6 XX XX XX XX',
                'email' => 'mvogo.jules@example.cm',
                // 'service_id' => null,
                'is_available' => true,
                'notes' => 'Direction de l\'Administration Centrale',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('drivers')->insert($drivers);
    }
}