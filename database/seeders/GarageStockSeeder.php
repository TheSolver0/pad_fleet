<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GarageStockSeeder extends Seeder
{
    private const LOCATION = 'garage';

    public function run(): void
    {
        $categories = $this->categories();
        $createdArticles = 0;
        $createdStocks = 0;

        DB::transaction(function () use ($categories, &$createdArticles, &$createdStocks): void {
            foreach ($this->inventory() as [$number, $name, $initial, $entries, $exits, $remaining]) {
                $reference = sprintf('GAR-%04d', $number);
                $category = $categories[$this->categoryCode($name)];

                $article = Article::firstOrCreate(
                    ['reference' => $reference],
                    [
                        'name' => Str::title(Str::lower($name)),
                        'description' => sprintf(
                            'Import inventaire garage au 23/07/2026 — initial: %s; entrées: %s; sorties: %s.',
                            $initial,
                            $entries,
                            $exits,
                        ),
                        'article_category_id' => $category->id,
                        'unit' => $this->unit($name),
                        'min_stock_level' => 1,
                        'is_active' => true,
                    ],
                );

                if ($article->wasRecentlyCreated) {
                    $createdArticles++;
                }

                $stock = Stock::firstOrCreate(
                    ['article_id' => $article->id, 'location' => self::LOCATION],
                    ['quantity' => $remaining, 'reserved_quantity' => 0],
                );

                if ($stock->wasRecentlyCreated) {
                    $createdStocks++;
                }
            }
        });

        $this->command?->info(
            "Stock garage: {$createdArticles} article(s) et {$createdStocks} ligne(s) de stock créés; existants conservés."
        );
    }

    /** @return array<string, ArticleCategory> */
    private function categories(): array
    {
        $definitions = [
            'GAR-PNEU' => ['Pneumatiques', 'Pneus et accessoires pneumatiques'],
            'GAR-BATT' => ['Batteries', 'Batteries et accessoires électriques'],
            'GAR-FILT' => ['Filtres', 'Filtres à air, huile, carburant et habitacle'],
            'GAR-FREIN' => ['Freinage', 'Pièces et fluides du système de freinage'],
            'GAR-EMBR' => ['Embrayage', 'Disques, plateaux et butées d\'embrayage'],
            'GAR-FLUI' => ['Lubrifiants et fluides', 'Huiles, graisses, gaz et produits techniques'],
            'GAR-ROUE' => ['Jantes et roues', 'Jantes, valves et accessoires de roues'],
            'GAR-ELEC' => ['Électricité et éclairage', 'Équipements électriques, alarmes et éclairage'],
            'GAR-SECU' => ['Sécurité', 'Équipements de sécurité et de protection'],
            'GAR-OUTI' => ['Outillage et équipements', 'Outillage et équipements d\'atelier'],
            'GAR-PIEC' => ['Pièces et accessoires', 'Autres pièces et accessoires du garage'],
        ];

        $categories = [];
        foreach ($definitions as $code => [$name, $description]) {
            $categories[$code] = ArticleCategory::query()
                ->where('code', $code)
                ->orWhere('name', $name)
                ->first()
                ?? ArticleCategory::create([
                    'code' => $code,
                    'name' => $name,
                    'description' => $description,
                    'is_active' => true,
                ]);
        }

        return $categories;
    }

    private function categoryCode(string $name): string
    {
        $name = Str::upper($name);

        return match (true) {
            Str::contains($name, 'PNEU') => 'GAR-PNEU',
            Str::contains($name, 'BATTERIE') => 'GAR-BATT',
            Str::contains($name, 'FILTRE') => 'GAR-FILT',
            Str::contains($name, ['EMBRAYAGE', 'BUTEE']) => 'GAR-EMBR',
            Str::contains($name, ['PLAQUETTE', 'GARNITURE', 'FREIN']) => 'GAR-FREIN',
            Str::contains($name, ['HUILE', 'GRAISSE', 'DEGRIPANT', 'ANTIFUITE', 'ANTI FUITE', 'FREON', 'GAZ']) => 'GAR-FLUI',
            Str::contains($name, ['JANTE', 'VALVE', 'ROUE']) => 'GAR-ROUE',
            Str::contains($name, ['CASQUE', 'SECURITE', 'EXTINCTEUR', 'PHARMACIE']) => 'GAR-SECU',
            Str::contains($name, ['AMPOULE', 'PHARE', 'ALARME', 'DEMARREUR', 'CABLE', 'COSSE', 'GYROPHARE', 'SIREN']) => 'GAR-ELEC',
            Str::contains($name, [
                'CLE ', 'CRIC', 'PINCE', 'COMPRESS', 'KARCHER', 'SOUDURE', 'PISTOLET',
                'SCIE', 'SERVANTE', 'ETAU', 'MULTIMETRE', 'ASPIRATEUR', 'TENAILLE',
                'CHALUMEAU', 'SERINGUE', 'BONBONNE', 'TAPIS MECANICIEN',
            ]) => 'GAR-OUTI',
            default => 'GAR-PIEC',
        };
    }

    private function unit(string $name): string
    {
        $name = Str::upper($name);

        return match (true) {
            Str::contains($name, 'HUILE 40 SAC') => 'sac',
            Str::contains($name, 'LIQUIDE DE FREIN LITRE') => 'litre',
            default => 'unité',
        };
    }

    /**
     * Snapshot du récapitulatif du fichier « SUIVI STOCK GARAGE MODIF.xlsx ».
     * Colonnes: numéro, article, stock initial, entrées, sorties, stock restant.
     *
     * @return array<int, array{int, string, int|float, int|float, int|float, int|float}>
     */
    private function inventory(): array
    {
        return [
            [1, 'ALARME', 31, 0, 0, 31],
            [2, 'ALLUME CIGARETTE', 4, 0, 0, 4],
            [3, 'AMORTISSEUR ARRIERE COASTER', 2, 0, 0, 2],
            [4, 'AMORTISSEUR AVANT', 1, 0, 0, 1],
            [5, 'AMORTISSEUR AVANT MOTO', 23, 0, 0, 23],
            [6, 'AMORTISSEUR HILUX/FORTUNER', 26, 0, 2, 24],
            [7, 'AMPOULE H7 30008', 3, 0, 0, 3],
            [8, 'AMPOULE LAND CRUISER', 7, 0, 0, 7],
            [9, 'AMPOULE LED 10W', 4, 0, 0, 4],
            [10, 'AMPOULE MITSUBUSHI', 15, 0, 0, 15],
            [11, 'ANTI FUITE TOTAL A BOUTEILLE BEN RADIATEUR 300 ML', 20, 0, 0, 20],
            [12, 'ANTI RADIATEUR TOTAL A FLACON COURT 300', 30, 0, 0, 30],
            [13, 'ANTI-DEMARRAGE', 19, 0, 0, 19],
            [14, 'ANTI-RUST SPRAY LUBRICANT', 71, 0, 0, 71],
            [15, 'ASPIRATEUR D\'HUILE', 1, 0, 0, 1],
            [16, 'ASPIRATEUR KARCHER', 3, 0, 0, 3],
            [17, 'ASPIRATEUR PNEUMATIQUE', 1, 0, 0, 1],
            [18, 'BAGUETTE DE SOUDURE 2,5', 1, 0, 0, 1],
            [19, 'BALAI ESSUIE GLACE HILUX', 12, 0, 0, 12],
            [20, 'BATTERIE GENERAL', 3, 0, 0, 3],
            [21, 'BATTERIE GENERAL 12V100 AH', 5, 0, 0, 5],
            [22, 'BATTERIE GENERAL 12V180 AH', 2, 0, 2, 0],
            [23, 'BATTERIE GENERAL 12V200 AH', 2, 0, 0, 2],
            [24, 'BATTERIE GENERAL 12V45 AH', 3, 0, 0, 3],
            [25, 'BATTERIE GENERAL 12V62 AH', 2, 0, 1, 1],
            [26, 'BATTERIE GENERAL 12V80 AH', 1, 0, 0, 1],
            [27, 'BATTERIE GENERAL 12V95 AH', 10, 0, 8, 2],
            [28, 'BATTERIE VARTA 12V190 AH OU  200 AH', 2, 0, 2, 0],
            [29, 'BATTERIE VARTA 12V45 AH', 0, 0, 0, 0],
            [30, 'BATTERIE VARTA 12V60 AH OU 62 AH', 0, 0, 0, 0],
            [31, 'BATTERIE VARTA 12V75 AH', 27, 0, 16, 11],
            [32, 'BATTERIE VARTA 12V95 AH', 23, 0, 8, 15],
            [33, 'BEC A CHALUMEAU', 2, 0, 0, 2],
            [34, 'BLOC VOLANT DE SECURITE', 63, 0, 0, 63],
            [35, 'BOITE A PHARMACIE', 52, 0, 0, 52],
            [36, 'BONBONNE DE VIDANGE', 1, 0, 0, 1],
            [37, 'BOULONS A ROUE', 46, 0, 0, 46],
            [38, 'BRAS DE CRICS DE 3 PCES', 18, 0, 0, 18],
            [39, 'BUTEE D\'EMBRAYAGE HARD BODY', 10, 0, 0, 10],
            [40, 'BUTEE D\'EMBRAYAGE HILUX', 2, 0, 0, 2],
            [41, 'CABLE DE RENFORCEMENT 3,5-16 MM2', 1, 0, 0, 1],
            [42, 'CABLE DE RENFORCEMENT 400A-2,5 MM2', 1, 0, 0, 1],
            [43, 'CALE DE SECURITE A JOINT', 3, 0, 0, 3],
            [44, 'CASQUE DE SECURITE MOTO', 10, 0, 0, 10],
            [45, 'CASQUE DE SECURITE SIMPLE', 6, 0, 0, 6],
            [46, 'CASQUE DE TRAVAIL', 6, 0, 0, 6],
            [47, 'CHAINE DE TRANSMISSION', 2, 0, 0, 2],
            [48, 'CHARGEUR DE BATTERIE YATO', 1, 0, 0, 1],
            [49, 'CHEVRON DE SECURITE', 45, 0, 0, 45],
            [50, 'CLE A MOLETTE', 3, 0, 0, 3],
            [51, 'CLE A PIPE MOB 10', 0, 0, 0, 0],
            [52, 'CLE A PIPE MOB 11', 2, 0, 0, 2],
            [53, 'CLE A PIPE MOB 12', 3, 0, 0, 3],
            [54, 'CLE A PIPE MOB 15', 3, 0, 0, 3],
            [55, 'CLE A PIPE MOB 16', 3, 0, 0, 3],
            [56, 'CLE A PIPE MOB 17', 3, 0, 0, 3],
            [57, 'CLE A PIPE MOB 21', 4, 0, 0, 4],
            [58, 'CLE A PIPE MOB 22', 2, 0, 0, 2],
            [59, 'CLE A PIPE MOB 23', 1, 0, 0, 1],
            [60, 'CLE A PIPE MOB 32', 2, 0, 0, 2],
            [61, 'CLE A ROUE EN METAL', 1, 0, 0, 1],
            [62, 'CLE A TORSE (MOB)', 5, 0, 0, 5],
            [63, 'CLE A TORSE (TOPTEL)', 2, 0, 0, 2],
            [64, 'CLE DYNAMOMETRIQUE FACOM', 1, 0, 0, 1],
            [65, 'CLE DYNAMOMETRIQUE MOB', 1, 0, 0, 1],
            [66, 'CLE MIXTE', 8, 0, 0, 8],
            [67, 'COFFRE A GENERAL', 1, 0, 0, 1],
            [68, 'COMPRESSEUR A AIR', 4, 0, 0, 4],
            [69, 'COMPRESSIOMETRE MONO DETENDEUR', 3, 0, 0, 3],
            [70, 'COMPRESSIOMETRE REGIME MOTEUR', 2, 0, 0, 2],
            [71, 'COMPRESSORT D\'AIR 180W', 3, 0, 0, 3],
            [72, 'COMPRESSORT D\'AIR 250W', 2, 0, 0, 2],
            [73, 'COSSE DE BATTERIE', 27, 0, 10, 17],
            [74, 'COURINNE DE TRANSMISSION MOTO EMBRAYAGE MOTO', 3, 0, 0, 3],
            [75, 'COURROIES ALLESOIRE HILUX', 6, 0, 0, 6],
            [76, 'COURROIES DISTRIBUTEUR', 3, 0, 1, 2],
            [77, 'CRIC DE LEVAGE', 4, 0, 0, 4],
            [78, 'CRIC DE LEVAGE YATO', 1, 0, 0, 1],
            [79, 'CRIC DE ROUE', 20, 0, 3, 17],
            [80, 'CRIC HYDRAULIQUE', 1, 0, 0, 1],
            [81, 'CRIC RED-BIG', 1, 0, 0, 1],
            [82, 'DEGRIPANT CARTON', 15, 0, 0, 15],
            [83, 'DEGRIPANT PM', 1, 0, 0, 1],
            [84, 'DEMARREUR', 3, 0, 0, 3],
            [85, 'DEMARREUR HILUX HIACE', 2, 0, 0, 2],
            [86, 'DISQUE DE FREIN', 16, 0, 4, 12],
            [87, 'DISQUE DE FREIN AV COROLLA', 8, 0, 0, 8],
            [88, 'DISQUE DE FREIN FORTUNER AV', 4, 0, 0, 4],
            [89, 'DISQUE DE FREIN HILUX AV', 2, 0, 0, 2],
            [90, 'DISQUE D\'EMBRAYAGE', 14, 0, 5, 9],
            [91, 'DISQUE D\'EMBRAYAGE AVANZA', 2, 0, 0, 2],
            [92, 'EMBRAYAGE COROLLA', 19, 0, 2, 17],
            [93, 'EMBRAYAGE HARD BODY', 2, 0, 0, 2],
            [94, 'ESSUIE GLACE COURT', 13, 0, 0, 13],
            [95, 'ESSUIE GLACE LONG', 1, 0, 0, 1],
            [96, 'ETAU A SERVAGE TOTAL', 1, 0, 0, 1],
            [97, 'EXTINCTEUR', 19, 0, 0, 19],
            [98, 'FILTRE A AIR', 48, 0, 2, 46],
            [99, 'FILTRE A AIR 3008', 39, 0, 0, 39],
            [100, 'FILTRE A AIR AVENSIS', 2, 0, 0, 2],
            [101, 'FILTRE A AIR CAMION', 4, 0, 0, 4],
            [102, 'FILTRE A AIR CAMIONNETTE', 4, 0, 0, 4],
            [103, 'FILTRE A AIR COROLLA', 25, 0, 0, 25],
            [104, 'FILTRE A AIR ERTIGA RUMION', 1, 0, 0, 1],
            [105, 'FILTRE A AIR GRAND', 12, 0, 0, 12],
            [106, 'FILTRE A AIR HARD BODY', 6, 0, 0, 6],
            [107, 'FILTRE A AIR HILUX', 2, 0, 0, 2],
            [108, 'FILTRE A AIR HILUX FORTUNER', 67, 0, 10, 57],
            [109, 'FILTRE A AIR IVECO', 8, 0, 0, 8],
            [110, 'FILTRE A AIR MAN', 6, 0, 3, 3],
            [111, 'FILTRE A AIR MICOTA', 6, 0, 0, 6],
            [112, 'FILTRE A AIR PRADO', 1, 0, 0, 1],
            [113, 'FILTRE A ESSENCE', 2, 0, 0, 2],
            [114, 'FILTRE A ESSENCE HENGST', 4, 0, 0, 4],
            [115, 'FILTRE A ESSENCE RENKEN', 9, 0, 0, 9],
            [116, 'FILTRE A GASOIL', 6, 0, 2, 4],
            [117, 'FILTRE A GASOIL BUS COASTER', 3, 0, 0, 3],
            [118, 'FILTRE A GASOIL FORTUNER/HILUX', 207, 0, 2, 205],
            [119, 'FILTRE A GASOIL HIACE', 5, 0, 0, 5],
            [120, 'FILTRE A GASOIL MITSUBUSHI L200', 11, 0, 0, 11],
            [121, 'FILTRE A HUILE 3008', 25, 0, 0, 25],
            [122, 'FILTRE A HUILE CAMION IVECO', 2, 0, 0, 2],
            [123, 'FILTRE A HUILE CAMION RENAULT', 3, 0, 0, 3],
            [124, 'FILTRE A HUILE COROLLA', 1, 0, 0, 1],
            [125, 'FILTRE A HUILE FORTUNER', 20, 0, 0, 20],
            [126, 'FILTRE A HUILE HILUX FORTUNER', 71, 0, 0, 71],
            [127, 'FILTRE A HUILE HILUX MOTEUR 5L', 98, 0, 10, 88],
            [128, 'FILTRE A HUILE LAND CRUISER', 2, 0, 1, 1],
            [129, 'FILTRE A HUILE MITSUBISHI L200', 33, 0, 0, 33],
            [130, 'FILTRE A HUILE PEUGEOT CITROEN', 13, 0, 0, 13],
            [131, 'FILTRE A HUILE RENKEN', 7, 0, 0, 7],
            [132, 'FILTRE A HUILE RUSH RUMION', 192, 0, 0, 192],
            [133, 'FILTRE A HUILE SUZUKI ERTIGA', 27, 0, 0, 27],
            [134, 'FILTRE A HUILE TOYOTA COROLLA', 14, 0, 0, 14],
            [135, 'FILTRE A POLLEN', 1, 0, 0, 1],
            [136, 'FILTRE A POLLEN CITROEN', 7, 0, 0, 7],
            [137, 'FILTRE A POLLEN CITROEN PEUGEOT A MOLE', 2, 0, 0, 2],
            [138, 'FILTRE A POLLEN ERTIGA', 1, 0, 0, 1],
            [139, 'FILTRE A POLLEN FORTUNER HILUX', 165, 0, 0, 165],
            [140, 'FILTRE A POLLEN HARD-BODY', 1, 0, 0, 1],
            [141, 'FILTRE A POLLEN HILUX FORTUNER', 10, 0, 0, 10],
            [142, 'FILTRE A POLLEN HILUX GRAND MODEL MOTEUR 5L', 10, 0, 0, 10],
            [143, 'FILTRE A POLLEN MITSUBISHI', 1, 0, 0, 1],
            [144, 'FILTRE A POLLEN MITSUBISHI', 8, 0, 0, 8],
            [145, 'FILTRE A POLLEN SUPER MAX', 10, 0, 0, 10],
            [146, 'FILTRE ACTIF DECANTEUR', 42, 0, 0, 42],
            [147, 'FILTRE CITROEN-PEUGEOT', 6, 0, 0, 6],
            [148, 'FILTRE DE CONDENSATEUR', 15, 0, 0, 15],
            [149, 'FILTRE DE SEPARATEUR D\'EAU', 4, 0, 0, 4],
            [150, 'FILTRE DIVERS', 4, 0, 0, 4],
            [151, 'FUEL FILTER', 9, 0, 0, 9],
            [152, 'GARNITURE DE FREIN AVANZA', 3, 0, 0, 3],
            [153, 'GARNITURE DE FREIN ERTIGA', 2, 0, 0, 2],
            [154, 'GARNITURE DE FREIN HILUX', 26, 0, 0, 26],
            [155, 'GARNITURE DE FREIN HILUX AR', 23, 0, 4, 19],
            [156, 'GARNITURE DE FREIN MITSUBUSHI L200', 3, 0, 4, -1],
            [157, 'GARNITURE DE FREIN RUMION-ERTIGA', 29, 0, 0, 29],
            [158, 'GARNITURE DE FREIN SUZUKI', 3, 0, 0, 3],
            [159, 'GARNITURE DE FREIN TOYOTA 040', 4, 0, 0, 4],
            [160, 'GARNITURE DE FREIN TOYOTA HILUX', 27, 0, 0, 27],
            [161, 'GAZ FREON R134 A', 9, 0, 1, 8],
            [162, 'GRAISSE TOTAL MULTIS', 14, 0, 0, 14],
            [163, 'GYROPHARE VEHICULE 12V', 1, 0, 0, 1],
            [164, 'HABILLAGE ERTIGA', 0, 0, 0, 0],
            [165, 'HABILLAGE FORTUNER', 12, 0, 0, 12],
            [166, 'HABILLAGE HARD BODY', 7, 0, 0, 7],
            [167, 'HABILLAGE HILUX', 13, 0, 0, 13],
            [168, 'HOUSSE DE VEHICULE TOYOTA HILUX', 15, 0, 0, 15],
            [169, 'HUILE 40 SAC', 9.5, 0, 0, 9.5],
            [170, 'HUILE DE COMPRESSEUR', 10, 0, 0, 10],
            [171, 'HUILE HILUX', 5, 0, 0, 5],
            [172, 'HUILE MOTEUR', 49, 0, 0, 49],
            [173, 'HUILE MOTEUR 10W40 QUARTZ TOTAL', 15, 0, 12, 3],
            [174, 'HUILE MOTEUR BIDON 5L', 6, 0, 0, 6],
            [175, 'HUILE OSCAR HARD BODY', 16, 0, 0, 16],
            [176, 'JANTE ALU', 5, 0, 0, 5],
            [177, 'JANTE ALU 16 POUCES', 4, 0, 0, 4],
            [178, 'JANTE ALU 17 POUCES MAX LOAD', 2, 0, 0, 2],
            [179, 'JANTE ALU-NOIR', 4, 0, 0, 4],
            [180, 'JANTE CAMION', 2, 0, 0, 2],
            [181, 'JANTE DE MOTO', 10, 0, 0, 10],
            [182, 'JANTE EN FER MOINS LARGE', 5, 0, 0, 5],
            [183, 'JANTE EN FER NOIR PROFOND', 4, 0, 0, 4],
            [184, 'JEU DE POSE PIEDS', 20, 0, 0, 20],
            [185, 'KARCHER POUR LAVERIE', 1, 0, 0, 1],
            [186, 'LAMPE TENSION GM', 20, 0, 0, 20],
            [187, 'LIQUIDE ANTIFUITE RADIATEUR', 24, 0, 0, 24],
            [188, 'LIQUIDE DE FREIN CARTON', 4, 0, 0, 4],
            [189, 'LIQUIDE DE FREIN CITROEN', 24, 0, 0, 24],
            [190, 'LIQUIDE DE FREIN LITRE', 8.5, 0, 0, 8.5],
            [191, 'LIQUIDE DE FREIN PIECE', 21, 0, 0, 21],
            [192, 'MINI KARCHER', 16, 0, 0, 16],
            [193, 'MONO D\'AIR A PNEU', 2, 0, 0, 2],
            [194, 'MONO ONGULAIRE', 3, 0, 0, 3],
            [195, 'MORTISSEUR ARRIERE MOTO', 2, 0, 0, 2],
            [196, 'MULTIMETRE DIGITAL', 3, 0, 0, 3],
            [197, 'NETTOYANT WASH TOTAL', 14, 0, 0, 14],
            [198, 'PAIRE DE GANG DE SOUDURE', 3, 0, 0, 3],
            [199, 'PARE BUFFLE CHROME ARRIERE HILUX', 5, 0, 0, 5],
            [200, 'PARE BUFFLE CHROME AVANT HILUX', 8, 0, 0, 8],
            [201, 'PHARE AVANT DROIT SERVICE', 2, 0, 0, 2],
            [202, 'PINCE A SOUDURE NEGATIF', 1, 0, 0, 1],
            [203, 'PINCE A SOUDURE POSITIF', 6, 0, 0, 6],
            [204, 'PINCE A SOUDURE POSITIF WISEUP', 2, 0, 0, 2],
            [205, 'PINCE COUPANTE', 2, 0, 0, 2],
            [206, 'PISTOLET A GRAISSE', 4, 0, 0, 4],
            [207, 'PISTOLET PNEUMATIQUE', 1, 0, 0, 1],
            [208, 'PLAQUE FCO SMART', 2, 0, 0, 2],
            [209, 'PLAQUETTE 3008', 14, 0, 4, 10],
            [210, 'PLAQUETTE APC', 2, 0, 0, 2],
            [211, 'PLAQUETTE AV COASTER', 2, 0, 0, 2],
            [212, 'PLAQUETTE AV RUMION ERTIGA', 15, 0, 0, 15],
            [213, 'PLAQUETTE AZIMCO', 42, 0, 0, 42],
            [214, 'PLAQUETTE DE FREIN AV CIVILIAN', 2, 0, 0, 2],
            [215, 'PLAQUETTE DE FREIN AV PRADO HILUX', 3, 0, 0, 3],
            [216, 'PLAQUETTE DE FREIN HILUX FORTUNER', 6, 0, 1, 5],
            [217, 'PLAQUETTE DIVERS', 29, 0, 0, 29],
            [218, 'PLAQUETTE GER', 2, 0, 0, 2],
            [219, 'PLAQUETTE GREK', 2, 0, 0, 2],
            [220, 'PLAQUETTE HILUX SOKITA', 3, 0, 0, 3],
            [221, 'PLAQUETTE MODEL RAV4', 10, 0, 0, 10],
            [222, 'PLAQUETTE MONATEE', 2, 0, 0, 2],
            [223, 'PLAQUETTE SUPERFIT', 2, 0, 0, 2],
            [224, 'PLAQUETTE TOYOTA AR FORTUNER', 23, 0, 0, 23],
            [225, 'PLAQUETTE TOYOTA AV HIACE', 6, 0, 0, 6],
            [226, 'PLAQUETTE TOYOTA AV HILUX FORTUNER', 26, 0, 0, 26],
            [227, 'PLAQUETTE TOYOTA AV RUSH', 6, 0, 0, 6],
            [228, 'PLATEAU D\'EMBRAYAGE AVANZA', 3, 0, 0, 3],
            [229, 'PLATEAU D\'EMBRAYAGE COROLLA', 10, 0, 0, 10],
            [230, 'PLATEAU D\'EMBRAYAGE HILUX 5L', 9, 0, 4, 5],
            [231, 'PLATEAU D\'EMBRAYAGE SUZUKI', 2, 0, 0, 2],
            [232, 'PNEU 135/65 R16', 8, 0, 0, 8],
            [233, 'PNEU 165/65 R17', 1, 0, 0, 1],
            [234, 'PNEU 185/65 R15 RUMION ERTIGA', 21, 0, 0, 21],
            [235, 'PNEU 195/65 R15 RUMION ERTIGA', 45, 0, 5, 40],
            [236, 'PNEU 205R 16C L200', 7, 0, 0, 7],
            [237, 'PNEU 215/65 R16 RUSH', 6, 18, 0, 24],
            [238, 'PNEU 225/70R16 HILUX', 6, 0, 4, 2],
            [239, 'PNEU 228170R15', 1, 0, 0, 1],
            [240, 'PNEU 235/70R 16 HIACE', 2, 0, 0, 2],
            [241, 'PNEU 265/50R 20 LAND CRUISER', 2, 0, 0, 2],
            [242, 'PNEU 265/55 R20', 6, 0, 0, 6],
            [243, 'PNEU 265/65R 17', 44, 45, 66, 23],
            [244, 'PNEU 265/70R 16 HILUX-FORTUNER', 1, 0, 0, 1],
            [245, 'PNEU 275/60R20', 5, 0, 5, 0],
            [246, 'PNEU 275155819', 4, 0, 0, 4],
            [247, 'PNEU AVEC JANTE', 16, 0, 0, 16],
            [248, 'PNEU AVILAN', 6, 0, 0, 6],
            [249, 'PNEU CAMION 10.00R26', 1, 0, 0, 1],
            [250, 'PNEU CAMION 12R 22.8', 3, 0, 0, 3],
            [251, 'PNEU CAMION 318180 R22.5', 9, 0, 0, 9],
            [252, 'PNEU CHARGE 215175R 17.5- COASTER', 7, 0, 0, 7],
            [253, 'PNEU CHARGE CAMION', 2, 0, 0, 2],
            [254, 'PNEU CHARGE CAMION 315186 R22.5', 3, 0, 0, 3],
            [255, 'PNEU COASTER PNEU 200', 1, 0, 0, 1],
            [256, 'PNEU FARROAD', 8, 0, 0, 8],
            [257, 'PNEU FARROAD 245/70R 16', 5, 0, 5, 0],
            [258, 'PNEU GOODYEAR 205/65 R15', 7, 0, 0, 7],
            [259, 'PNEU GOODYEAR 225/55R75', 2, 0, 0, 2],
            [260, 'PNEU GOODYEAR 255/50R 120', 6, 0, 4, 2],
            [261, 'PNEU GRELANDER 235/60 R16', 3, 0, 0, 3],
            [262, 'PNEU GRELANDER 275/55 R19', 4, 0, 0, 4],
            [263, 'PNEU HACDA', 8, 0, 0, 8],
            [264, 'PNEU LANNIGATOR', 16, 0, 0, 16],
            [265, 'PNEU MAXTREK 225/55 R18', 1, 0, 0, 1],
            [266, 'PNEU MAXXIS', 1, 0, 0, 1],
            [267, 'PNEU MAXXIS 225/55 R16', 1, 0, 0, 1],
            [268, 'PNEU MAXXIS 225/60 R16', 1, 0, 0, 1],
            [269, 'PNEU MAXXIS 235/55 R17', 3, 0, 0, 3],
            [270, 'PNEU MICHELIN', 7, 0, 0, 7],
            [271, 'PNEU RAIDE 205/60 R16', 5, 0, 0, 5],
            [272, 'PNEU SAFERICH 235/70 R16', 1, 0, 0, 1],
            [273, 'PNEU SAFRICH', 10, 0, 0, 10],
            [274, 'PNEU SPORTWAY 225/55 R16', 4, 0, 0, 4],
            [275, 'PNEU TIGAR 225/55 R16', 1, 0, 0, 1],
            [276, 'PNEU ZEXTOUR 225/55 R16', 4, 0, 0, 4],
            [277, 'PNEU ZEXTOUR 265/70 R17', 3, 0, 0, 3],
            [278, 'PNEU275/55R20 LAND CRUISER', 0, 17, 8, 9],
            [279, 'PNEUS', 3, 0, 0, 3],
            [280, 'POSTE DE SOUDURE 2', 1, 0, 0, 1],
            [281, 'POSTE DE SOUDURE WISEUP', 2, 0, 0, 2],
            [282, 'POT DE FEU ROUGE', 4, 0, 0, 4],
            [283, 'POT DE FEU ROUGE AR', 1, 0, 0, 1],
            [284, 'POT DE FEU ROUGE DROIT COASTER AR', 1, 0, 0, 1],
            [285, 'POT DE PHARE ARRIERE DROIT TOYOTA HILUX', 4, 0, 0, 4],
            [286, 'POT DE PHARE ARRIERE GAUCHE TOYOTA HILUX', 4, 0, 0, 4],
            [287, 'POT DE PHARE FEU ROUGE AR GAUCHE ET DROIT COASTER', 2, 0, 0, 2],
            [288, 'POT DE PHARE HILUX', 3, 0, 0, 3],
            [289, 'POT DE PHARE HILUX GAUCHE', 6, 0, 0, 6],
            [290, 'REVOLUING WORNING LIGHT', 2, 0, 0, 2],
            [291, 'RIVETEUSE ACCORDÉON', 1, 0, 0, 1],
            [292, 'ROULEAU DE TUYAU A GAZ JUMAUX', 1, 0, 0, 1],
            [293, 'SANGUE DE REMORQUAGE SM', 1, 0, 0, 1],
            [294, 'SCIE A METAL', 12, 0, 0, 12],
            [295, 'SEINGLE DE REMORQUAGE DE SUMPENSA', 3, 0, 0, 3],
            [296, 'SERINGUE A HUILE', 1, 0, 0, 1],
            [297, 'SERVANTE A OUTIL COMPLET', 2, 0, 0, 2],
            [298, 'SIREN', 2, 0, 0, 2],
            [299, 'STARRTER/DEMARREUR TOYOTA HILUX/FORT', 1, 0, 0, 1],
            [300, 'TAPIS MECANICIEN', 1, 0, 0, 1],
            [301, 'TAPIS TOTAL', 1, 0, 0, 1],
            [302, 'TENAILLE MOB', 1, 0, 0, 1],
            [303, 'VALVE A ROUE', 268, 0, 0, 268],
            [304, 'VALVE DE ROUE HILUX FORTUNER', 40, 0, 0, 40],
        ];
    }
}
