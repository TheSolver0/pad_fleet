<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DrivingLicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $licenses = [
            // AYISSI NICOLAS DEMYRE - Driver ID: 1
            [
                'driver_id' => 1,
                'license_number' => 'CE-278792-14',
                'license_type' => 'Professionnel',
                'category' => 'A, B',
                'issue_date' => Carbon::parse('2014-11-18'),
                'expiry_date' => Carbon::parse('2024-11-18'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré - renouvellement nécessaire',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // ETOMO MENYE ALBERT SERGE - Driver ID: 2
            [
                'driver_id' => 2,
                'license_number' => 'CE-171371-12-01',
                'license_type' => 'Professionnel',
                'category' => 'BE, C, D, DE',
                'issue_date' => Carbon::parse('2012-06-10'),
                'expiry_date' => Carbon::parse('2020-06-10'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'driver_id' => 2,
                'license_number' => 'CE-171371-12',
                'license_type' => 'Professionnel',
                'category' => 'CE',
                'issue_date' => Carbon::parse('2012-06-10'),
                'expiry_date' => Carbon::parse('2020-06-10'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis catégorie CE',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // MOUSSOM ANDRE BERTRAND - Driver ID: 3
            [
                'driver_id' => 3,
                'license_number' => 'NW-114451-10-01',
                'license_type' => 'Professionnel',
                'category' => 'BE, CE, D, DE',
                'issue_date' => Carbon::parse('2014-02-22'),
                'expiry_date' => Carbon::parse('2024-02-22'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'driver_id' => 3,
                'license_number' => 'NW-114451-10',
                'license_type' => 'Professionnel',
                'category' => 'C',
                'issue_date' => Carbon::parse('2014-02-22'),
                'expiry_date' => Carbon::parse('2024-02-22'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis catégorie C',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // TOKEA MENGOUMOU AURELIEN BIENVENU - Driver ID: 4
            [
                'driver_id' => 4,
                'license_number' => 'CE-108217-09-01',
                'license_type' => 'Professionnel',
                'category' => 'A, B',
                'issue_date' => Carbon::parse('2014-07-09'),
                'expiry_date' => Carbon::parse('2024-07-09'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré récemment',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'driver_id' => 4,
                'license_number' => 'CE-108217-09',
                'license_type' => 'Professionnel',
                'category' => 'C',
                'issue_date' => Carbon::parse('2009-07-09'),
                'expiry_date' => Carbon::parse('2019-07-09'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis catégorie C expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'driver_id' => 4,
                'license_number' => 'CE-108217-09-02',
                'license_type' => 'Professionnel',
                'category' => 'D',
                'issue_date' => Carbon::parse('2009-07-09'),
                'expiry_date' => Carbon::parse('2019-07-09'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis catégorie D expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // NYEANCHI JOHN WIRBA - Driver ID: 5
            [
                'driver_id' => 5,
                'license_number' => 'NYEANCHI-2024-01',
                'license_type' => 'Professionnel',
                'category' => 'BE, C, CE, D, DE',
                'issue_date' => Carbon::parse('2014-06-28'),
                'expiry_date' => Carbon::parse('2024-06-28'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'driver_id' => 5,
                'license_number' => 'NYEANCHI-2021-01',
                'license_type' => 'Professionnel',
                'category' => 'BE',
                'issue_date' => Carbon::parse('2011-03-30'),
                'expiry_date' => Carbon::parse('2021-03-30'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Ancien permis catégorie BE',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // NYOLLO NGOMA JOSEPH - Driver ID: 6
            [
                'driver_id' => 6,
                'license_number' => 'CE-186695-12',
                'license_type' => 'Professionnel',
                'category' => 'B, C, D',
                'issue_date' => Carbon::parse('2012-10-17'),
                'expiry_date' => Carbon::parse('2022-10-17'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // WANDJI ATANGANA EUGENE STANISLAS - Driver ID: 7
            [
                'driver_id' => 7,
                'license_number' => 'LT-113664-10',
                'license_type' => 'Professionnel',
                'category' => 'A, B, C, D, E',
                'issue_date' => Carbon::parse('2010-05-28'),
                'expiry_date' => Carbon::parse('2020-05-28'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Toutes catégories - Permis expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // KOUEMO JEAN PAUL - Driver ID: 8
            [
                'driver_id' => 8,
                'license_number' => 'LT-127906-11',
                'license_type' => 'Professionnel',
                'category' => 'B, C, D',
                'issue_date' => Carbon::parse('2011-03-15'),
                'expiry_date' => Carbon::parse('2021-03-15'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // FOTSO TAKOUTSING FRANCOIS XAVIER - Driver ID: 9
            [
                'driver_id' => 9,
                'license_number' => 'LT-106639-09',
                'license_type' => 'Professionnel',
                'category' => 'B, C, D, E',
                'issue_date' => Carbon::parse('2014-03-28'),
                'expiry_date' => Carbon::parse('2024-03-28'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => false, // Expiré
                'notes' => 'Permis expiré',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // MVOGO JULES EMMANUEL - Driver ID: 10
            [
                'driver_id' => 10,
                'license_number' => 'YA-020098-05',
                'license_type' => 'Professionnel',
                'category' => 'A, B, C, D, E',
                'issue_date' => Carbon::parse('2015-02-08'),
                'expiry_date' => Carbon::parse('2025-02-08'),
                'issuing_authority' => 'Ministère des Transports',
                'issuing_country' => 'CM',
                'is_active' => true, // Encore valide jusqu'en 2025
                'notes' => 'Toutes catégories - Permis valide',
                'file_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('driving_licenses')->insert($licenses);
    }
}