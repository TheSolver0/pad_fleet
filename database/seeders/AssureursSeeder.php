<?php

namespace Database\Seeders;

use App\Models\Assureur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssureursSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Vider la table assureurs
        DB::table('assureurs')->delete();

        $assureurs = [
            [
                'name' => 'AXA Assurances Cameroun',
                'code' => 'AXA001',
                'contact_person' => 'Jean-Pierre Mvondo',
                'phone' => '+237 222 23 34 56',
                'email' => 'contact@axa.cm',
                'address' => 'Avenue Charles de Gaulle, Yaoundé',
                'city' => 'Yaoundé',
                'country' => 'Cameroun',
                'website' => 'https://www.axa.cm',
                'notes' => 'Compagnie d\'assurance française présente au Cameroun',
                'is_active' => true,
            ],
            [
                'name' => 'NSIA Assurances',
                'code' => 'NSI002',
                'contact_person' => 'Marie-Claire Ondo',
                'phone' => '+237 243 42 12 34',
                'email' => 'info@nsia.cmr',
                'address' => 'Rue des Fleurs, Douala',
                'city' => 'Douala',
                'country' => 'Cameroun',
                'website' => 'https://www.nsiagroup.com',
                'notes' => 'Groupe panafricain d\'assurance',
                'is_active' => true,
            ],
            [
                'name' => 'Société Camerounaise d\'Assurances (SCA)',
                'code' => 'SCA003',
                'contact_person' => 'Alain Nkodo',
                'phone' => '+237 233 45 67 89',
                'email' => 'commercial@sca.cm',
                'address' => 'Boulevard de la Liberté, Yaoundé',
                'city' => 'Yaoundé',
                'country' => 'Cameroun',
                'website' => 'https://www.sca.cm',
                'notes' => 'Compagnie d\'assurance camerounaise historique',
                'is_active' => true,
            ],
            [
                'name' => 'Allianz Cameroun',
                'code' => 'ALL004',
                'contact_person' => 'Patricia Essomba',
                'phone' => '+237 222 98 76 54',
                'email' => 'service@allianz.cm',
                'address' => 'Immeuble Allianz, Bonanjo, Douala',
                'city' => 'Douala',
                'country' => 'Cameroun',
                'website' => 'https://www.allianz.cm',
                'notes' => 'Groupe d\'assurance allemand',
                'is_active' => true,
            ],
            [
                'name' => 'AGET Cameroun',
                'code' => 'AGE005',
                'contact_person' => 'Roger Mballa',
                'phone' => '+237 242 11 22 33',
                'email' => 'contact@aget.cm',
                'address' => 'Avenue Kennedy, Yaoundé',
                'city' => 'Yaoundé',
                'country' => 'Cameroun',
                'website' => 'https://www.ages.cm',
                'notes' => 'Assurance Générale du Cameroun',
                'is_active' => true,
            ],
            [
                'name' => 'Mutuelle Agricole du Cameroun (MAC)',
                'code' => 'MAC006',
                'contact_person' => 'Sophie Tchoumi',
                'phone' => '+237 233 88 99 77',
                'email' => 'info@mac.cm',
                'address' => 'Route de Kumba, Bafoussam',
                'city' => 'Bafoussam',
                'country' => 'Cameroun',
                'website' => 'https://www.mac.cm',
                'notes' => 'Spécialisée dans l\'assurance agricole',
                'is_active' => true,
            ],
            [
                'name' => 'CICA-RE',
                'code' => 'CIC007',
                'contact_person' => 'Joseph Mengue',
                'phone' => '+237 222 55 44 33',
                'email' => 'reassurance@cica-re.com',
                'address' => 'Immeuble CICA, Yaoundé',
                'city' => 'Yaoundé',
                'country' => 'Cameroun',
                'website' => 'https://www.cica-re.com',
                'notes' => 'Compagnie de réassurance',
                'is_active' => true,
            ],
            [
                'name' => 'Old Mutual Cameroun',
                'code' => 'OLD008',
                'contact_person' => 'Esther Ngo',
                'phone' => '+237 243 66 77 88',
                'email' => 'clients@oldmutual.cm',
                'address' => 'Immeuble Old Mutual, Douala',
                'city' => 'Douala',
                'country' => 'Cameroun',
                'website' => 'https://www.oldmutual.cm',
                'notes' => 'Compagnie d\'assurance sud-africaine',
                'is_active' => true,
            ],
            [
                'name' => 'SOGACAM Assurance',
                'code' => 'SOG009',
                'contact_person' => 'Paul Etame',
                'phone' => '+237 222 33 44 55',
                'email' => 'assurance@sogacam.cm',
                'address' => 'Boulevard de la Réunification, Yaoundé',
                'city' => 'Yaoundé',
                'country' => 'Cameroun',
                'website' => 'https://www.sogacam.cm',
                'notes' => 'Filiale assurance de la SOGACAM',
                'is_active' => false, // Inactive pour démonstration
            ],
            [
                'name' => 'Prudential Cameroun',
                'code' => 'PRU010',
                'contact_person' => 'Cécile Ze',
                'phone' => '+237 243 22 11 00',
                'email' => 'info@prudential.cm',
                'address' => 'Immeuble Prudential, Douala',
                'city' => 'Douala',
                'country' => 'Cameroun',
                'website' => 'https://www.prudential.cm',
                'notes' => 'Compagnie d\'assurance britannique',
                'is_active' => true,
            ],
        ];

        foreach ($assureurs as $assureur) {
            Assureur::create($assureur);
        }

        $activeCount = Assureur::where('is_active', true)->count();
        $inactiveCount = Assureur::where('is_active', false)->count();

        $this->command->info(count($assureurs) . ' assureurs créés avec succès.');
        $this->command->info("- Assureurs actifs : {$activeCount}");
        $this->command->info("- Assureurs inactifs : {$inactiveCount}");
    }
}
