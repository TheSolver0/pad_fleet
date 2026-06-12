<?php

namespace Database\Seeders;

use App\Models\Driver;
use Database\Seeders\Support\PadDocumentParsers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = PadDocumentParsers::parsePersonnelDrivers();

        foreach ($drivers as $driver) {
            $emailSlug = Str::slug($driver['matricule'].'-'.$driver['full_name']);

            Driver::updateOrCreate(
                ['matricule' => $driver['matricule']],
                [
                    'first_name' => $driver['first_name'],
                    'last_name' => $driver['last_name'],
                    'phone' => null,
                    'email' => "{$emailSlug}@pad.local",
                    'is_available' => true,
                    'notes' => $driver['structure'],
                ]
            );
        }

        $this->command?->info(count($drivers).' chauffeurs importés depuis la liste du personnel PAD.');
    }
}
