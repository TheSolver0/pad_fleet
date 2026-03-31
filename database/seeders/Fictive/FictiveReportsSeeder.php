<?php

namespace Database\Seeders\Fictive;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Garage;
use App\Models\Mechanic;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FictiveReportsSeeder extends Seeder
{
    private int $years = 5;
    private bool $reset = false;
    private int $repairsCreated = 0;
    private int $partsCreated = 0;

    public function configure(int $years, bool $reset): self
    {
        $this->years = max(1, min(15, $years));
        $this->reset = $reset;

        return $this;
    }

    /**
     * @return array{repairs:int,parts:int,vehicles:int,years:int}
     */
    public function run(): array
    {
        $tag = 'FICTIVE_REPORTS';

        if ($this->reset) {
            $this->clearPreviousFictiveData($tag);
        }

        $garage = Garage::firstOrCreate(
            ['name' => 'Garage Fictif PAD'],
            ['type' => 'internal', 'is_active' => true]
        );

        $brand = Brand::firstOrCreate(
            ['name' => 'Toyota'],
            ['code' => 'TOYOT']
        );

        $modelNames = ['Hilux', 'Land Cruiser', 'Corolla', 'Prado', 'Hiace', 'Yaris'];
        foreach ($modelNames as $name) {
            VehicleModel::firstOrCreate(
                ['brand_id' => $brand->id, 'name' => $name],
                ['code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 8))]
            );
        }

        $models = VehicleModel::where('brand_id', $brand->id)->get();
        $mechanics = Mechanic::query()->where('is_active', true)->get();
        $articles = Article::query()->where('is_active', true)->whereNotNull('purchase_price')->get();

        if ($mechanics->isEmpty() || $models->isEmpty() || $articles->isEmpty()) {
            return [
                'repairs' => 0,
                'parts' => 0,
                'vehicles' => 0,
                'years' => $this->years,
            ];
        }

        $categories = ['leger', '4x4', 'bus', 'camionnette'];
        $vehicles = collect();

        // 12 vehicules fictifs dedies au reporting.
        for ($i = 1; $i <= 12; $i++) {
            $vehicle = Vehicle::updateOrCreate(
                ['registration' => sprintf('FICT-RPT-%03d', $i)],
                [
                    'vehicle_model_id' => $models[($i - 1) % $models->count()]->id,
                    'category' => $categories[($i - 1) % count($categories)],
                    'purchase_date' => now()->subYears(random_int(2, 8))->toDateString(),
                    'purchase_price' => 9000000 + ($i * 350000),
                    'venal_value' => 7000000 + ($i * 280000),
                    'mileage' => 15000 + ($i * 3000),
                    'status' => 'available',
                    'garage_id' => $garage->id,
                    'assignment_type' => 'pool',
                    'notes' => $tag,
                ]
            );

            $vehicles->push($vehicle);
        }

        // Donnees coherentes sur les N dernieres annees:
        // - 6 interventions par mois
        // - couts et durees plausibles
        // - evaluation qualite/delai renseignee
        $start = now()->subYears($this->years)->startOfMonth();
        $end = now()->endOfMonth();
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            for ($k = 0; $k < 6; $k++) {
                $vehicle = $vehicles[($k + (int) $cursor->format('n')) % $vehicles->count()];
                $mechanic = $mechanics[($k + (int) $cursor->format('m')) % $mechanics->count()];

                $startAt = $cursor->copy()->addDays(random_int(1, 26))->setTime(random_int(7, 10), random_int(0, 59));
                $expectedDays = random_int(1, 5);
                $actualDays = max(1, $expectedDays + random_int(-1, 2));
                $expectedAt = $startAt->copy()->addDays($expectedDays);
                $completedAt = $startAt->copy()->addDays($actualDays);

                $laborCost = random_int(15000, 180000);
                $type = random_int(1, 100) <= 30 ? Repair::TYPE_EXTERNAL : Repair::TYPE_INTERNAL;
                $priority = [Repair::PRIORITY_LOW, Repair::PRIORITY_MEDIUM, Repair::PRIORITY_HIGH, Repair::PRIORITY_URGENT][random_int(0, 3)];
                $repairType = ['maintenance', 'panne', 'carrosserie'][random_int(0, 2)];

                $repair = Repair::create([
                    'vehicle_id' => $vehicle->id,
                    'garage_id' => $garage->id,
                    'mechanic_id' => $mechanic->id,
                    'type' => $type,
                    'description' => "Intervention fictive {$repairType} - {$cursor->format('m/Y')}",
                    'repair_type' => $repairType,
                    'priority' => $priority,
                    'estimated_duration' => $expectedDays * 8,
                    'cost' => $laborCost,
                    'started_at' => $startAt,
                    'expected_completed_at' => $expectedAt,
                    'completed_at' => $completedAt,
                    'quality_rating' => random_int(3, 5),
                    'delay_rating' => max(1, 5 - max(0, $actualDays - $expectedDays)),
                    'evaluation_comment' => 'Donnee fictive de test rapports (qualite/delai).',
                    'evaluated_at' => $completedAt->copy()->addHours(2),
                    'notes' => $tag,
                    'created_at' => $startAt,
                    'updated_at' => $completedAt,
                ]);
                $this->repairsCreated++;

                $partsCount = random_int(1, 4);
                for ($p = 0; $p < $partsCount; $p++) {
                    $article = $articles[random_int(0, $articles->count() - 1)];
                    $qty = random_int(1, 4);
                    $unitPrice = (float) ($article->purchase_price ?? random_int(1500, 25000));

                    RepairPart::create([
                        'repair_id' => $repair->id,
                        'article_id' => $article->id,
                        'quantity_used' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $qty * $unitPrice,
                        'stock_location' => random_int(0, 1) === 0 ? 'main' : 'garage',
                        'notes' => 'Piece fictive pour rapport',
                        'created_at' => $startAt->copy()->addHours(1),
                        'updated_at' => $startAt->copy()->addHours(1),
                    ]);
                    $this->partsCreated++;
                }
            }

            $cursor->addMonth();
        }

        return [
            'repairs' => $this->repairsCreated,
            'parts' => $this->partsCreated,
            'vehicles' => $vehicles->count(),
            'years' => $this->years,
        ];
    }

    private function clearPreviousFictiveData(string $tag): void
    {
        $repairIds = Repair::query()
            ->where('notes', $tag)
            ->pluck('id');

        if ($repairIds->isNotEmpty()) {
            RepairPart::query()->whereIn('repair_id', $repairIds)->delete();
            Repair::query()->whereIn('id', $repairIds)->delete();
        }

        Vehicle::query()
            ->where('registration', 'like', 'FICT-RPT-%')
            ->delete();
    }
}

