<?php

namespace App\Livewire\Portal\Reports;

use App\Models\Vehicle;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\VehicleSchedule;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VehicleConsumption extends Component
{
    public string $period = 'month'; // 'week', 'month', 'quarter', 'year'
    public string $vehicle_category = '';
    public string $start_date = '';
    public string $end_date = '';

    public function mount(): void
    {
        $this->start_date = now()->subMonth()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
    }

    public function updatedPeriod(): void
    {
        $this->updateDateRange();
    }

    private function updateDateRange(): void
    {
        $now = now();
        
        match($this->period) {
            'week' => [
                $this->start_date = $now->startOfWeek()->format('Y-m-d'),
                $this->end_date = $now->endOfWeek()->format('Y-m-d')
            ],
            'month' => [
                $this->start_date = $now->startOfMonth()->format('Y-m-d'),
                $this->end_date = $now->endOfMonth()->format('Y-m-d')
            ],
            'quarter' => [
                $this->start_date = $now->startOfQuarter()->format('Y-m-d'),
                $this->end_date = $now->endOfQuarter()->format('Y-m-d')
            ],
            'year' => [
                $this->start_date = $now->startOfYear()->format('Y-m-d'),
                $this->end_date = $now->endOfYear()->format('Y-m-d')
            ],
            default => []
        };
    }

    public function getConsumptionByVehicleTypeProperty(): array
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        $query = Vehicle::query();

        if ($this->vehicle_category !== '') {
            $query->where('category', $this->vehicle_category);
        }

        $vehicles = $query->with(['repairs' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])
              ->with('repairParts.article');
        }])->get();

        $consumption = [];

        foreach ($vehicles as $vehicle) {
            $category = $vehicle->category ?? 'non classé';
            
            if (!isset($consumption[$category])) {
                $consumption[$category] = [
                    'category' => $category,
                    'vehicle_count' => 0,
                    'total_repairs' => 0,
                    'total_parts_cost' => 0,
                    'total_distance' => 0,
                    'total_fuel_consumed' => 0,
                    'average_fuel_per_100km' => 0,
                    'parts_used' => [],
                ];
            }

            $consumption[$category]['vehicle_count']++;
            
            // Calculer les réparations et coûts
            foreach ($vehicle->repairs as $repair) {
                $consumption[$category]['total_repairs']++;
                $consumption[$category]['total_parts_cost'] += $repair->repairParts->sum('total_price');
                
                // Regrouper les pièces utilisées
                foreach ($repair->repairParts as $part) {
                    $partName = $part->article->name;
                    if (!isset($consumption[$category]['parts_used'][$partName])) {
                        $consumption[$category]['parts_used'][$partName] = [
                            'name' => $partName,
                            'quantity' => 0,
                            'total_cost' => 0,
                        ];
                    }
                    $consumption[$category]['parts_used'][$partName]['quantity'] += $part->quantity_used;
                    $consumption[$category]['parts_used'][$partName]['total_cost'] += $part->total_price;
                }
            }
        }

        // Calculer les distances et consommation de carburant
        $schedules = VehicleSchedule::whereBetween('start_datetime', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereHas('vehicle', function ($q) {
                if ($this->vehicle_category !== '') {
                    $q->where('category', $this->vehicle_category);
                }
            })
            ->with('vehicle')
            ->get();

        foreach ($schedules as $schedule) {
            $category = $schedule->vehicle->category ?? 'non classé';
            if (isset($consumption[$category])) {
                $consumption[$category]['total_distance'] += $schedule->actual_distance ?? 0;
                $consumption[$category]['total_fuel_consumed'] += $schedule->fuel_consumed ?? 0;
            }
        }

        // Calculer la moyenne de consommation
        foreach ($consumption as $category => &$data) {
            if ($data['total_distance'] > 0) {
                $data['average_fuel_per_100km'] = ($data['total_fuel_consumed'] / $data['total_distance']) * 100;
            }
            
            // Trier les pièces utilisées par coût total
            uasort($data['parts_used'], function ($a, $b) {
                return $b['total_cost'] <=> $a['total_cost'];
            });
        }

        return $consumption;
    }

    public function getTopConsumedPartsProperty(): array
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        $parts = RepairPart::whereHas('repair', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->whereHas('repair.vehicle', function ($q) {
            if ($this->vehicle_category !== '') {
                $q->where('category', $this->vehicle_category);
            }
        })
        ->with('article')
        ->get()
        ->groupBy('article.name')
        ->map(function ($group) {
            return [
                'name' => $group->first()->article->name,
                'reference' => $group->first()->article->reference,
                'total_quantity' => $group->sum('quantity_used'),
                'total_cost' => $group->sum('total_price'),
                'usage_count' => $group->count(),
            ];
        })
        ->sortByDesc('total_cost')
        ->take(20)
        ->toArray();

        return $parts;
    }

    public function getTireAlertsProperty(): array
    {
        $alerts = [];
        
        // Vérifier les pneus par catégorie de véhicule
        $tireArticles = \App\Models\Article::whereNotNull('tire_size')
            ->where('is_active', true)
            ->with(['stocks' => function ($q) {
                $q->selectRaw('article_id, SUM(quantity) as total_quantity')
                  ->groupBy('article_id');
            }])
            ->get();

        foreach ($tireArticles as $tire) {
            $totalStock = $tire->stocks->sum('total_quantity');
            
            if ($totalStock <= 2) { // Alert si moins de 2 pneus
                $alerts[] = [
                    'tire_size' => $tire->tire_size,
                    'article_name' => $tire->name,
                    'stock_quantity' => $totalStock,
                    'compatible_vehicles' => $tire->compatible_vehicle_categories_label,
                    'alert_level' => $totalStock <= 0 ? 'critical' : 'warning',
                ];
            }
        }

        return $alerts;
    }

    public function render(): View
    {
        return view('livewire.portal.reports.vehicle-consumption', [
            'consumptionByType' => $this->consumptionByVehicleType,
            'topParts' => $this->topConsumedParts,
            'tireAlerts' => $this->tireAlerts,
        ]);
    }
}
