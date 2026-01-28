<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Direction', 'code' => 'DIR'],
            ['name' => 'Ressources Humaines', 'code' => 'RH'],
            ['name' => 'Finances', 'code' => 'FIN'],
            ['name' => 'Logistique', 'code' => 'LOG'],
            ['name' => 'Technique', 'code' => 'TEC'],
        ];
        foreach ($items as $item) {
            Service::firstOrCreate(['code' => $item['code']], $item);
        }
    }
}
