<?php

namespace App\Livewire\Portal\Reports;

use App\Models\Mission;
use App\Models\Repair;
use App\Models\Sinistre;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public string $period = 'month'; // month, quarter, year

    public function getStatsProperty(): array
    {
        $start = match ($this->period) {
            'year' => now()->startOfYear(),
            'quarter' => now()->startOfQuarter(),
            default => now()->startOfMonth(),
        };
        $end = now();

        $missions = Mission::whereBetween('date_start', [$start, $end])->where('status', Mission::STATUS_COMPLETED);
        $totalMissions = (clone $missions)->count();
        $totalDistance = (clone $missions)->sum('distance_km');

        $repairs = Repair::whereBetween('created_at', [$start, $end]);
        $totalRepairs = (clone $repairs)->count();
        $totalRepairCost = (clone $repairs)->sum('cost');

        $sinistres = Sinistre::whereBetween('declared_at', [$start, $end]);
        $totalSinistres = (clone $sinistres)->count();
        $estimatedSinistreCost = (clone $sinistres)->sum('estimated_cost');

        $vehiclesCount = Vehicle::count();
        $availableCount = Vehicle::where('status', Vehicle::STATUS_AVAILABLE)->count();
        $repairCount = Vehicle::where('status', Vehicle::STATUS_REPAIR)->count();

        return [
            'vehicles_total' => $vehiclesCount,
            'vehicles_available' => $availableCount,
            'vehicles_repair' => $repairCount,
            'missions_count' => $totalMissions,
            'missions_distance' => $totalDistance,
            'repairs_count' => $totalRepairs,
            'repairs_cost' => $totalRepairCost,
            'sinistres_count' => $totalSinistres,
            'sinistres_estimated_cost' => $estimatedSinistreCost,
        ];
    }

    public function render(): View
    {
        return view('livewire.portal.reports.index', [
            'stats' => $this->stats,
        ])->layout('layouts.app', ['title' => 'Rapports']);
    }
}
