<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\DrivingLicense;
use Carbon\Carbon;
use Database\Seeders\Support\PadDocumentParsers;
use Illuminate\Database\Seeder;

class DrivingLicenseSeeder extends Seeder
{
    public function run(): void
    {
        $records = PadDocumentParsers::parseDrivingLicenses();
        $imported = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $driver = Driver::query()->where('matricule', $record['matricule'])->first();

            if (! $driver) {
                $nameParts = PadDocumentParsers::splitName($record['full_name']);
                $driver = Driver::create([
                    'matricule' => $record['matricule'],
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => strtolower($record['matricule']).'@pad.local',
                    'is_available' => true,
                    'notes' => $record['structure'],
                ]);
            }

            foreach ($record['licenses'] as $license) {
                if ($license['expiry_date'] === null) {
                    $skipped++;
                    continue;
                }

                /** @var Carbon $expiryDate */
                $expiryDate = $license['expiry_date'];
                $issueDate = $expiryDate->copy()->subYears(10);
                $isActive = $expiryDate->greaterThanOrEqualTo(now()->startOfDay());

                DrivingLicense::updateOrCreate(
                    ['license_number' => $license['license_number']],
                    [
                        'driver_id' => $driver->id,
                        'license_type' => 'Professionnel',
                        'category' => $license['category'] ?? 'B',
                        'issue_date' => $issueDate,
                        'expiry_date' => $expiryDate,
                        'issuing_authority' => 'Ministère des Transports',
                        'issuing_country' => 'CM',
                        'is_active' => $isActive,
                        'notes' => $license['notes'],
                    ]
                );

                $imported++;
            }
        }

        $this->command?->info("{$imported} permis importés, {$skipped} entrées sans date ignorées.");
    }
}
