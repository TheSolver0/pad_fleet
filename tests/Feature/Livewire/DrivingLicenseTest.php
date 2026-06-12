<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Portal\Drivers\DrivingLicenses as DrivingLicensesIndex;
use App\Models\DrivingLicense;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Feature\FeatureTestCase;
use Tests\Support\FleetTestData;

class DrivingLicenseTest extends FeatureTestCase
{
    public function test_can_create_driving_license(): void
    {
        $driver = FleetTestData::driver();

        Livewire::test(DrivingLicensesIndex::class)
            ->set('driver_id', $driver->id)
            ->set('license_number', 'LIC-2026-001')
            ->set('license_type', 'B')
            ->set('category', 'B')
            ->set('issue_date', '2024-01-01')
            ->set('expiry_date', now()->addYear()->format('Y-m-d'))
            ->set('issuing_authority', 'MINTRANSPORT')
            ->set('issuing_country', 'CM')
            ->call('saveLicense')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $license = DrivingLicense::where('license_number', 'LIC-2026-001')->first();
        $this->assertNotNull($license);
        $this->assertFalse($license->isExpired());
        $this->assertTrue($license->expiry_date->greaterThan(now()->addMonths(6)));
    }

    public function test_expired_license_is_detected(): void
    {
        $license = DrivingLicense::create([
            'driver_id' => FleetTestData::driver()->id,
            'license_number' => 'LIC-EXP-001',
            'license_type' => 'B',
            'category' => 'B',
            'issue_date' => '2020-01-01',
            'expiry_date' => now()->subDay()->format('Y-m-d'),
            'issuing_authority' => 'MINTRANSPORT',
            'issuing_country' => 'CM',
            'is_active' => true,
        ]);

        $this->assertTrue($license->isExpired());
        $this->assertSame('Expiré', $license->status);
    }

    public function test_can_upload_license_scan(): void
    {
        Storage::fake('public');
        $driver = FleetTestData::driver();
        $file = UploadedFile::fake()->image('permis.jpg');

        Livewire::test(DrivingLicensesIndex::class)
            ->set('driver_id', $driver->id)
            ->set('license_number', 'LIC-UP-001')
            ->set('license_type', 'B')
            ->set('category', 'B')
            ->set('issue_date', '2024-06-01')
            ->set('expiry_date', '2027-06-01')
            ->set('issuing_authority', 'MINTRANSPORT')
            ->set('issuing_country', 'CM')
            ->set('license_file', $file)
            ->call('saveLicense')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $license = DrivingLicense::where('license_number', 'LIC-UP-001')->first();
        $this->assertNotNull($license->file_path);
        Storage::disk('public')->assertExists($license->file_path);
    }
}
