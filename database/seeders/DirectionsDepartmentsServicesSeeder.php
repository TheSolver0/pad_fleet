<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Direction;
use App\Models\OrgService;
use Illuminate\Database\Seeder;

class DirectionsDepartmentsServicesSeeder extends Seeder
{
    public function run(): void
    {
        $directionsData = [
            ['code' => 'CDA', 'name' => "Centre des Archives"],
            ['code' => 'DAP', 'name' => "Direction des Aménagements Portuaires"],
            ['code' => 'CQN', 'name' => "Cellule Qualité et Normalisation"],
            ['code' => 'DAJ', 'name' => "Direction des Affaires Juridiques"],
            ['code' => 'DCRP', 'name' => "Direction de la Communication et des Relations Publiques"],
            ['code' => 'DCAP', 'name' => "Direction de la Capitainerie"],
            ['code' => 'DDLM', 'name' => "Direction du Développement Logistique et Maritime"],
            ['code' => 'DFC', 'name' => "Direction des Finances et de la Comptabilité"],
            ['code' => 'DSR', 'name' => "Direction du Suivi et de la Relance"],
            ['code' => 'DG', 'name' => "Direction Générale"],
            ['code' => 'DRH', 'name' => "Direction des Ressources Humaines"],
            ['code' => 'DEX', 'name' => "Direction des Opérations"],
            ['code' => 'SAPF', 'name' => "Service Accueil et Protocoles"],
            ['code' => 'DSI', 'name' => "Direction des Systèmes d'Information"],
            ['code' => 'DAPC', 'name' => "Direction des Affaires Portuaires et Concessions"],
            ['code' => 'DCG', 'name' => "Direction du Contrôle de Gestion"],
            ['code' => 'DAI', 'name' => "Direction de l'Audit Interne"],
            ['code' => 'DAG', 'name' => "Direction des Affaires Générales"],
            ['code' => 'CTIPB', 'name' => "Comité Technique Interministériel des Projets du Port"],
            ['code' => 'CDDRCR', 'name' => "Cellule de Développement Durable et Responsabilité Civile et Réglementaire"],
        ];

        $departmentsByDirection = [
            'CDA' => [
                ['name' => 'Archives courantes', 'code' => 'AC'],
                ['name' => 'Archives intermédiaires', 'code' => 'AI'],
                ['name' => 'Numérisation et conservation', 'code' => 'NC'],
            ],
            'DAP' => [
                ['name' => 'Études et conception', 'code' => 'EC'],
                ['name' => 'Travaux et ouvrages', 'code' => 'TO'],
                ['name' => 'Suivi des chantiers', 'code' => 'SC'],
            ],
            'CQN' => [
                ['name' => 'Qualité et processus', 'code' => 'QP'],
                ['name' => 'Normalisation et certification', 'code' => 'NC'],
            ],
            'DAJ' => [
                ['name' => 'Contentieux', 'code' => 'CTX'],
                ['name' => 'Conventions et contrats', 'code' => 'CC'],
                ['name' => 'Conformité et réglementation', 'code' => 'CR'],
            ],
            'DCRP' => [
                ['name' => 'Communication interne', 'code' => 'CI'],
                ['name' => 'Relations presse et médias', 'code' => 'RPM'],
                ['name' => 'Événementiel', 'code' => 'EVT'],
            ],
            'DCAP' => [
                ['name' => 'Circulation portuaire', 'code' => 'CP'],
                ['name' => 'Sécurité navale', 'code' => 'SN'],
                ['name' => 'Pilotage et remorquage', 'code' => 'PR'],
            ],
            'DDLM' => [
                ['name' => 'Développement logistique', 'code' => 'DL'],
                ['name' => 'Partenariats maritimes', 'code' => 'PM'],
                ['name' => 'Projets stratégiques', 'code' => 'PS'],
            ],
            'DFC' => [
                ['name' => 'Comptabilité générale', 'code' => 'CG'],
                ['name' => 'Trésorerie', 'code' => 'TR'],
                ['name' => 'Budget et prévisions', 'code' => 'BP'],
            ],
            'DSR' => [
                ['name' => 'Recouvrement', 'code' => 'REC'],
                ['name' => 'Suivi clients et créances', 'code' => 'SCC'],
            ],
            'DG' => [
                ['name' => 'Secrétariat général', 'code' => 'SG'],
                ['name' => 'Stratégie et développement', 'code' => 'SD'],
                ['name' => 'Coordination inter-directions', 'code' => 'CID'],
            ],
            'DRH' => [
                ['name' => 'Recrutement', 'code' => 'REC'],
                ['name' => 'Formation', 'code' => 'FOR'],
                ['name' => 'Paie et administration du personnel', 'code' => 'PAP'],
                ['name' => 'Affaires sociales et conditions de travail', 'code' => 'ASCT'],
            ],
            'DEX' => [
                ['name' => 'Exploitation à quai', 'code' => 'EQ'],
                ['name' => 'Manutention', 'code' => 'MAN'],
                ['name' => 'Trafic et planification', 'code' => 'TP'],
            ],
            'SAPF' => [
                ['name' => 'Accueil et renseignements', 'code' => 'AR'],
                ['name' => 'Protocole et invitations', 'code' => 'PI'],
                ['name' => 'Organisation d\'événements', 'code' => 'OE'],
            ],
            'DSI' => [
                ['name' => 'Infrastructure et réseaux', 'code' => 'IR'],
                ['name' => 'Développement et applications', 'code' => 'DA'],
                ['name' => 'Support et assistance', 'code' => 'SA'],
            ],
            'DAPC' => [
                ['name' => 'Concessions et contrats', 'code' => 'CC'],
                ['name' => 'Tarification portuaire', 'code' => 'TP'],
                ['name' => 'Affaires commerciales', 'code' => 'AC'],
            ],
            'DCG' => [
                ['name' => 'Reporting et tableaux de bord', 'code' => 'RTB'],
                ['name' => 'Budgets et prévisions', 'code' => 'BP'],
                ['name' => 'Performance et analyse', 'code' => 'PA'],
            ],
            'DAI' => [
                ['name' => 'Audit opérationnel', 'code' => 'AO'],
                ['name' => 'Audit financier', 'code' => 'AF'],
                ['name' => 'Mission d\'enquête', 'code' => 'ME'],
            ],
            'DAG' => [
                ['name' => 'Patrimoine et bâtiments', 'code' => 'PB'],
                ['name' => 'Marchés publics', 'code' => 'MP'],
                ['name' => 'Génie civil et maintenance', 'code' => 'GCM'],
            ],
            'CTIPB' => [
                ['name' => 'Projets d\'investissement', 'code' => 'PI'],
                ['name' => 'Suivi des travaux', 'code' => 'ST'],
                ['name' => 'Coordination technique', 'code' => 'CT'],
            ],
            'CDDRCR' => [
                ['name' => 'Environnement et impact', 'code' => 'EI'],
                ['name' => 'RSE et développement durable', 'code' => 'RSEDD'],
                ['name' => 'Conformité réglementaire', 'code' => 'CR'],
            ],
        ];

        $servicesByDirectionDept = [
            'CDA' => ['AC' => [['name' => 'Classement courrier', 'code' => 'CLC'], ['name' => 'Consultation sur place', 'code' => 'CSP']], 'AI' => [['name' => 'Conservation intermédiaire', 'code' => 'CIN'], ['name' => 'Élimination réglementée', 'code' => 'ER']], 'NC' => [['name' => 'Numérisation documents', 'code' => 'ND'], ['name' => 'Conservation long terme', 'code' => 'CLT']]],
            'DAP' => ['EC' => [['name' => 'Études techniques', 'code' => 'ET'], ['name' => 'Appels d\'offres', 'code' => 'AO']], 'TO' => [['name' => 'Ouvrages maritimes', 'code' => 'OM'], ['name' => 'Voirie et réseaux', 'code' => 'VR']], 'SC' => [['name' => 'Pilotage chantiers', 'code' => 'PC'], ['name' => 'Contrôle qualité travaux', 'code' => 'CQT']]],
            'CQN' => ['QP' => [['name' => 'Procédures qualité', 'code' => 'PQ'], ['name' => 'Audits internes', 'code' => 'AUDI']], 'NC' => [['name' => 'Normes ISO', 'code' => 'ISO'], ['name' => 'Certifications', 'code' => 'CERT']]],
            'DAJ' => ['CTX' => [['name' => 'Contentieux civil', 'code' => 'CC'], ['name' => 'Contentieux commercial', 'code' => 'CCO']], 'CC' => [['name' => 'Rédaction conventions', 'code' => 'RC'], ['name' => 'Négociation contrats', 'code' => 'NCO']], 'CR' => [['name' => 'Veille réglementaire', 'code' => 'VR'], ['name' => 'Conformité', 'code' => 'CONF']]],
            'DCRP' => ['CI' => [['name' => 'Intranet et annonces', 'code' => 'IA'], ['name' => 'Newsletter', 'code' => 'NEW']], 'RPM' => [['name' => 'Relations presse', 'code' => 'RP'], ['name' => 'Médias sociaux', 'code' => 'MS']], 'EVT' => [['name' => 'Organisation séminaires', 'code' => 'OS'], ['name' => 'Cérémonies', 'code' => 'CER']]],
            'DCAP' => ['CP' => [['name' => 'Autorisation d\'escale', 'code' => 'AE'], ['name' => 'Mouvements navires', 'code' => 'MN']], 'SN' => [['name' => 'Sécurité des navires', 'code' => 'SDN'], ['name' => 'Prévention pollution', 'code' => 'PP']], 'PR' => [['name' => 'Service pilote', 'code' => 'SP'], ['name' => 'Remorquage', 'code' => 'REM']]],
            'DDLM' => ['DL' => [['name' => 'Optimisation logistique', 'code' => 'OL'], ['name' => 'Chaîne logistique', 'code' => 'CL']], 'PM' => [['name' => 'Partenariats armateurs', 'code' => 'PAR'], ['name' => 'Accords commerciaux', 'code' => 'AC']], 'PS' => [['name' => 'Projets d\'investissement', 'code' => 'PIN'], ['name' => 'Études stratégiques', 'code' => 'ES']]],
            'DFC' => ['CG' => [['name' => 'Comptabilité générale', 'code' => 'CG'], ['name' => 'Clôture et bilan', 'code' => 'CB']], 'TR' => [['name' => 'Trésorerie courante', 'code' => 'TC'], ['name' => 'Placement et financement', 'code' => 'PF']], 'BP' => [['name' => 'Budgets direction', 'code' => 'BD'], ['name' => 'Prévisions financières', 'code' => 'PF']]],
            'DSR' => ['REC' => [['name' => 'Relance clients', 'code' => 'RCL'], ['name' => 'Contentieux recouvrement', 'code' => 'CTR']], 'SCC' => [['name' => 'Suivi créances', 'code' => 'SCR'], ['name' => 'Rapports clients', 'code' => 'RAPC']]],
            'DG' => ['SG' => [['name' => 'Secrétariat DG', 'code' => 'SDG'], ['name' => 'Courrier et plis', 'code' => 'CP']], 'SD' => [['name' => 'Plan stratégique', 'code' => 'PST'], ['name' => 'Projets transverses', 'code' => 'PT']], 'CID' => [['name' => 'Coordination projets', 'code' => 'COOP'], ['name' => 'Synthèses direction', 'code' => 'SYD']]],
            'DRH' => ['REC' => [['name' => 'Recrutement cadres', 'code' => 'RCA'], ['name' => 'Recrutement non-cadres', 'code' => 'RNC']], 'FOR' => [['name' => 'Formation interne', 'code' => 'FI'], ['name' => 'Développement compétences', 'code' => 'DC']], 'PAP' => [['name' => 'Paie', 'code' => 'PAI'], ['name' => 'Administration personnel', 'code' => 'ADP']], 'ASCT' => [['name' => 'Médecine du travail', 'code' => 'MT'], ['name' => 'Action sociale', 'code' => 'AS']]],
            'DEX' => ['EQ' => [['name' => 'Exploitation quai est', 'code' => 'EQE'], ['name' => 'Exploitation quai ouest', 'code' => 'EQO']], 'MAN' => [['name' => 'Manutention vrac', 'code' => 'MV'], ['name' => 'Manutention conteneurs', 'code' => 'MCT']], 'TP' => [['name' => 'Planification escales', 'code' => 'PE'], ['name' => 'Statistiques trafic', 'code' => 'ST']]],
            'SAPF' => ['AR' => [['name' => 'Standard et accueil', 'code' => 'SA'], ['name' => 'Renseignements', 'code' => 'REN']], 'PI' => [['name' => 'Invitations officielles', 'code' => 'IO'], ['name' => 'Protocole visites', 'code' => 'PV']], 'OE' => [['name' => 'Événements internes', 'code' => 'EI'], ['name' => 'Réceptions', 'code' => 'REP']]],
            'DSI' => ['IR' => [['name' => 'Réseau et serveurs', 'code' => 'RS'], ['name' => 'Sécurité informatique', 'code' => 'SI']], 'DA' => [['name' => 'Applications métier', 'code' => 'AM'], ['name' => 'Développement web', 'code' => 'DW']], 'SA' => [['name' => 'Help desk', 'code' => 'HD'], ['name' => 'Maintenance postes', 'code' => 'MP']]],
            'DAPC' => ['CC' => [['name' => 'Gestion concessions', 'code' => 'GC'], ['name' => 'Contrats portuaires', 'code' => 'CPO']], 'TP' => [['name' => 'Tarifs et redevances', 'code' => 'TR'], ['name' => 'Facturation portuaire', 'code' => 'FP']], 'AC' => [['name' => 'Relations clients', 'code' => 'RCL'], ['name' => 'Offres commerciales', 'code' => 'OFC']]],
            'DCG' => ['RTB' => [['name' => 'Tableaux de bord', 'code' => 'TDB'], ['name' => 'Reporting mensuel', 'code' => 'RPM']], 'BP' => [['name' => 'Budgets opérationnels', 'code' => 'BO'], ['name' => 'Prévisions annuelles', 'code' => 'PRA']], 'PA' => [['name' => 'Indicateurs performance', 'code' => 'IP'], ['name' => 'Analyse écarts', 'code' => 'AE']]],
            'DAI' => ['AO' => [['name' => 'Audit process', 'code' => 'AP'], ['name' => 'Audit sites', 'code' => 'AS']], 'AF' => [['name' => 'Audit comptable', 'code' => 'AC'], ['name' => 'Audit financier', 'code' => 'AF']], 'ME' => [['name' => 'Enquêtes internes', 'code' => 'ENQ'], ['name' => 'Signalements', 'code' => 'SIG']]],
            'DAG' => ['PB' => [['name' => 'Gestion patrimoniale', 'code' => 'GP'], ['name' => 'Bâtiments', 'code' => 'BAT']], 'MP' => [['name' => 'Consultation marchés', 'code' => 'CM'], ['name' => 'Attribution contrats', 'code' => 'ATC']], 'GCM' => [['name' => 'Travaux neufs', 'code' => 'TN'], ['name' => 'Maintenance BTP', 'code' => 'MBTP']]],
            'CTIPB' => ['PI' => [['name' => 'Études projets', 'code' => 'EP'], ['name' => 'Montage dossiers', 'code' => 'MD']], 'ST' => [['name' => 'Pilotage travaux', 'code' => 'PT'], ['name' => 'Réception ouvrages', 'code' => 'RO']], 'CT' => [['name' => 'Coordination technique', 'code' => 'COT'], ['name' => 'Interface maître d\'ouvrage', 'code' => 'IMO']]],
            'CDDRCR' => ['EI' => [['name' => 'Études d\'impact', 'code' => 'EIM'], ['name' => 'Suivi environnemental', 'code' => 'SEN']], 'RSEDD' => [['name' => 'Politique RSE', 'code' => 'PRSE'], ['name' => 'Indicateurs RSE', 'code' => 'IRSE']], 'CR' => [['name' => 'Conformité réglementaire', 'code' => 'COR'], ['name' => 'Veille normative', 'code' => 'VN']]],
        ];

        foreach ($directionsData as $d) {
            Direction::updateOrCreate(
                ['code' => $d['code']],
                ['name' => $d['name'], 'notes' => null]
            );
        }

        $directionIds = Direction::all()->keyBy('code');

        foreach ($departmentsByDirection as $dirCode => $depts) {
            $direction = $directionIds->get($dirCode);
            if (!$direction) {
                continue;
            }
            foreach ($depts as $dept) {
                $department = Department::updateOrCreate(
                    [
                        'direction_id' => $direction->id,
                        'code' => $dept['code'],
                    ],
                    [
                        'name' => $dept['name'],
                        'notes' => null,
                    ]
                );
                $services = $servicesByDirectionDept[$dirCode][$dept['code']] ?? null;
                if ($services) {
                    foreach ($services as $srv) {
                        OrgService::updateOrCreate(
                            [
                                'department_id' => $department->id,
                                'code' => $srv['code'],
                            ],
                            [
                                'name' => $srv['name'],
                                'notes' => null,
                            ]
                        );
                    }
                } else {
                    foreach (
                        [
                            ['name' => $dept['name'] . ' — Pôle 1', 'code' => $dept['code'] . '-1'],
                            ['name' => $dept['name'] . ' — Pôle 2', 'code' => $dept['code'] . '-2'],
                        ] as $srv
                    ) {
                        OrgService::updateOrCreate(
                            ['department_id' => $department->id, 'code' => $srv['code']],
                            ['name' => $srv['name'], 'notes' => null]
                        );
                    }
                }
            }
        }
    }
}
