<?php

namespace App\Livewire\Portal\Reports;

use App\Models\Mission;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\Sinistre;
use App\Models\StockMovement;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public string $period = 'month'; // month, quarter, year
    public string $vehicle_category = '';
    public string $start_date = '';
    public string $end_date = '';

    public function mount(): void
    {
        $this->syncDatesFromPeriod();
    }

    public function updatedPeriod(): void
    {
        $this->syncDatesFromPeriod();
    }

    private function syncDatesFromPeriod(): void
    {
        $now = now();
        $start = match ($this->period) {
            'year' => $now->copy()->startOfYear(),
            'quarter' => $now->copy()->startOfQuarter(),
            default => $now->copy()->startOfMonth(),
        };
        $end = $now->copy();

        $this->start_date = $start->format('Y-m-d');
        $this->end_date = $end->format('Y-m-d');
    }

    public function getStatsProperty(): array
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->endOfDay();
        $category = $this->vehicle_category;

        $missions = Mission::whereBetween('date_start', [$start, $end])->where('status', Mission::STATUS_COMPLETED);
        $totalMissions = (clone $missions)->count();
        $totalDistance = (clone $missions)->sum('distance_km');

        $repairs = Repair::whereBetween('created_at', [$start, $end])
            ->when($category !== '', fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('category', $category)));
        $totalRepairs = (clone $repairs)->count();
        $totalRepairCost = (clone $repairs)->sum('cost');

        $sinistres = Sinistre::whereBetween('declared_at', [$start, $end]);
        $totalSinistres = (clone $sinistres)->count();
        $estimatedSinistreCost = (clone $sinistres)->sum('estimated_cost');

        $parts = RepairPart::query()
            ->whereHas('repair', fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($category !== '', fn ($q) => $q->whereHas('repair.vehicle', fn ($vq) => $vq->where('category', $category)));
        $partsQty = (int) (clone $parts)->sum('quantity_used');
        $partsCost = (float) (clone $parts)->sum('total_price');

        $stockMovements = StockMovement::query()->whereBetween('created_at', [$start, $end]);
        $stockEntries = (clone $stockMovements)->where('type', StockMovement::TYPE_ENTRY);
        $stockExits = (clone $stockMovements)->where('type', StockMovement::TYPE_EXIT);

        $vehiclesCount = Vehicle::count();
        $availableCount = Vehicle::where('status', Vehicle::STATUS_AVAILABLE)->count();
        $repairCount = Vehicle::where('status', Vehicle::STATUS_REPAIR)->count();

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'vehicle_category' => $category,
            'vehicles_total' => $vehiclesCount,
            'vehicles_available' => $availableCount,
            'vehicles_repair' => $repairCount,
            'missions_count' => $totalMissions,
            'missions_distance' => $totalDistance,
            'repairs_count' => $totalRepairs,
            'repairs_cost' => $totalRepairCost,
            'sinistres_count' => $totalSinistres,
            'sinistres_estimated_cost' => $estimatedSinistreCost,
            'parts_quantity' => $partsQty,
            'parts_cost' => $partsCost,
            'stock_entries_qty' => (int) (clone $stockEntries)->sum('quantity'),
            'stock_entries_cost' => (float) (clone $stockEntries)->sum('total_cost'),
            'stock_exits_qty' => (int) (clone $stockExits)->sum('quantity'),
            'stock_exits_cost' => (float) (clone $stockExits)->sum('total_cost'),
        ];
    }

    /** Coût maintenance (WorkOrders) par véhicule sur la période */
    public function getMaintenanceCostByVehicleProperty(): \Illuminate\Support\Collection
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end   = Carbon::parse($this->end_date)->endOfDay();
        $category = $this->vehicle_category;

        return WorkOrder::query()
            ->join('vehicles', 'work_orders.vehicle_id', '=', 'vehicles.id')
            ->whereBetween('work_orders.work_date', [$start, $end])
            ->whereNotNull('work_orders.total_cost')
            ->when($category !== '', fn ($q) => $q->where('vehicles.category', $category))
            ->selectRaw('
                vehicles.id as vehicle_id,
                vehicles.registration,
                vehicles.category,
                count(work_orders.id) as wo_count,
                coalesce(sum(work_orders.labor_cost), 0) as total_labor,
                coalesce(sum(work_orders.parts_cost), 0) as total_parts,
                coalesce(sum(work_orders.total_cost), 0) as total_cost
            ')
            ->groupBy('vehicles.id', 'vehicles.registration', 'vehicles.category')
            ->orderByDesc('total_cost')
            ->limit(15)
            ->get();
    }

    /** Coût maintenance (WorkOrders) par type de véhicule sur la période */
    public function getMaintenanceCostByCategoryProperty(): \Illuminate\Support\Collection
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end   = Carbon::parse($this->end_date)->endOfDay();

        return WorkOrder::query()
            ->join('vehicles', 'work_orders.vehicle_id', '=', 'vehicles.id')
            ->whereBetween('work_orders.work_date', [$start, $end])
            ->whereNotNull('work_orders.total_cost')
            ->selectRaw('
                vehicles.category,
                count(work_orders.id) as wo_count,
                coalesce(sum(work_orders.labor_cost), 0) as total_labor,
                coalesce(sum(work_orders.parts_cost), 0) as total_parts,
                coalesce(sum(work_orders.total_cost), 0) as total_cost
            ')
            ->groupBy('vehicles.category')
            ->orderByDesc('total_cost')
            ->get()
            ->map(function ($row) {
                $row->category_label = Vehicle::categoryOptions()[$row->category] ?? $row->category;
                return $row;
            });
    }

    /** Évolution mensuelle du coût maintenance sur 12 mois glissants */
    public function getMaintenanceCostTrendProperty(): array
    {
        $labels  = [];
        $costs   = [];
        $category = $this->vehicle_category;

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            $costs[]  = (float) WorkOrder::query()
                ->join('vehicles', 'work_orders.vehicle_id', '=', 'vehicles.id')
                ->whereYear('work_orders.work_date', $month->year)
                ->whereMonth('work_orders.work_date', $month->month)
                ->whereNotNull('work_orders.total_cost')
                ->when($category !== '', fn ($q) => $q->where('vehicles.category', $category))
                ->sum('work_orders.total_cost');
        }

        return ['labels' => $labels, 'costs' => $costs];
    }

    public function render(): View
    {
        return view('livewire.portal.reports.index', [
            'stats'                    => $this->stats,
            'maintenanceCostByVehicle' => $this->maintenanceCostByVehicle,
            'maintenanceCostByCategory'=> $this->maintenanceCostByCategory,
            'maintenanceCostTrend'     => $this->maintenanceCostTrend,
        ])->layout('layouts.app', ['title' => 'Rapports']);
    }
}
