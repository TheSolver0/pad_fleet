<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Douala', 'code' => 'DLA', 'region' => 'Littoral', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Yaoundé', 'code' => 'YDE', 'region' => 'Centre', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Bafoussam', 'code' => 'BFA', 'region' => 'Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Garoua', 'code' => 'GAR', 'region' => 'Nord', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Bamenda', 'code' => 'BDA', 'region' => 'Nord-Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Maroua', 'code' => 'MRA', 'region' => 'Extrême-Nord', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Ngaoundéré', 'code' => 'NGA', 'region' => 'Adamaoua', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Bertoua', 'code' => 'BERT', 'region' => 'Est', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Edea', 'code' => 'EDA', 'region' => 'Littoral', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Kribi', 'code' => 'KRI', 'region' => 'Sud', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Limbe', 'code' => 'LIM', 'region' => 'Sud-Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Buea', 'code' => 'BUE', 'region' => 'Sud-Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Kumba', 'code' => 'KUM', 'region' => 'Sud-Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Foumban', 'code' => 'FOU', 'region' => 'Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Dschang', 'code' => 'DSCH', 'region' => 'Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Mbouda', 'code' => 'MBA', 'region' => 'Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Kumbo', 'code' => 'KUMBO', 'region' => 'Nord-Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Fundong', 'code' => 'FUN', 'region' => 'Nord-Ouest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Mokolo', 'code' => 'MOK', 'region' => 'Extrême-Nord', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Kousséri', 'code' => 'KOU', 'region' => 'Extrême-Nord', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Yagoua', 'code' => 'YAG', 'region' => 'Extrême-Nord', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Meiganga', 'code' => 'MEI', 'region' => 'Adamaoua', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Tibati', 'code' => 'TIB', 'region' => 'Adamaoua', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Batouri', 'code' => 'BAT', 'region' => 'Est', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Abong-Mbang', 'code' => 'ABO', 'region' => 'Est', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('cities')->insert($cities);
    }
}
