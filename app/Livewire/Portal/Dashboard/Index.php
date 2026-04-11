<?php

namespace App\Livewire\Portal\Dashboard;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\InsuranceContractGlobal;
use App\Models\Mission;
use App\Models\Repair;
use App\Models\Sinistre;
use App\Models\Vehicle;
use Livewire\Component;


class Index extends Component
{
    public string $tripPeriod = 'month';
    protected $paginationTheme = 'bootstrap'; 


    public function getKpis(): array
    {
        $total = Vehicle::count();
        $available = Vehicle::where('status', Vehicle::STATUS_AVAILABLE)->count();
        $inUse = Vehicle::where('status', Vehicle::STATUS_IN_USE)->count();
        $repair = Vehicle::where('status', Vehicle::STATUS_REPAIR)->count();
        $outOfService = Vehicle::where('status', Vehicle::STATUS_OUT_OF_SERVICE)->count();

        $repairsOngoing = Repair::whereNull('completed_at')->count();
        $sinistresOpen = Sinistre::whereIn('status', [
            Sinistre::STATUS_DECLARED,
            Sinistre::STATUS_IN_REPAIR,
        ])->count();

        /*$missionsThisMonth = Mission::whereMonth('date_start', now()->month)
            ->whereYear('date_start', now()->year)
            ->whereIn('status', [Mission::STATUS_APPROVED, Mission::STATUS_COMPLETED])
            ->count();*/

        $missionsThisMonth = Mission::count();

        $insurancesExpiring = InsuranceContractGlobal::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->count();

        $rate = $total > 0 ? round((float) $available / $total * 100, 1) : 0;

        return [
            'total_vehicles' => $total,
            'available' => $available,
            'in_use' => $inUse,
            'repair' => $repair,
            'out_of_service' => $outOfService,
            'repairs_ongoing' => $repairsOngoing,
            'sinistres_open' => $sinistresOpen,
            'missions_this_month' => $missionsThisMonth,
            'insurances_expiring_30' => $insurancesExpiring,
            'availability_rate' => $rate,
        ];
    }

    public function getVehiclesByStatusChart(): array
    {
        $data = Vehicle::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $order = [
            Vehicle::STATUS_AVAILABLE,
            Vehicle::STATUS_IN_USE,
            Vehicle::STATUS_REPAIR,
            Vehicle::STATUS_OUT_OF_SERVICE,
        ];
        $labels = [
            Vehicle::STATUS_AVAILABLE => 'Disponibles',
            Vehicle::STATUS_IN_USE => 'En mission',
            Vehicle::STATUS_REPAIR => 'En réparation',
            Vehicle::STATUS_OUT_OF_SERVICE => 'Hors service',
        ];
        $colors = [
            Vehicle::STATUS_AVAILABLE => 'rgba(122, 144, 0, 0.85)',
            Vehicle::STATUS_IN_USE => 'rgba(26, 84, 144, 0.85)',
            Vehicle::STATUS_REPAIR => 'rgba(0, 184, 212, 0.85)',
            Vehicle::STATUS_OUT_OF_SERVICE => 'rgba(201, 107, 107, 0.85)',
        ];

        $names = [];
        $values = [];
        $colorList = [];
        foreach ($order as $status) {
            $names[] = $labels[$status];
            $values[] = (int) ($data[$status] ?? 0);
            $colorList[] = $colors[$status];
        }

        return [
            'names' => $names,
            'values' => $values,
            'colors' => $colorList,
        ];
    }

    public function getVehiclesByCategoryChart(): array
    {
        $data = Vehicle::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $options = Vehicle::categoryOptions();
        $names = [];
        $values = [];
        $palette = [
            'rgba(26, 84, 144, 0.85)',
            'rgba(0, 184, 212, 0.85)',
            'rgba(122, 144, 0, 0.85)',
            'rgba(184, 212, 0, 0.6)',
            'rgba(100, 120, 160, 0.85)',
            'rgba(60, 140, 180, 0.85)',
            'rgba(201, 107, 107, 0.7)',
            'rgba(140, 140, 140, 0.75)',
        ];

        foreach ($options as $key => $label) {
            $cnt = (int) ($data[$key] ?? 0);
            if ($cnt > 0) {
                $names[] = $label;
                $values[] = $cnt;
            }
        }

        $otherKeys = array_diff(array_keys($data), array_keys($options));
        if (! empty($otherKeys)) {
            $otherCount = 0;
            foreach ($otherKeys as $k) {
                $otherCount += (int) ($data[$k] ?? 0);
            }
            if ($otherCount > 0) {
                $names[] = 'Autre';
                $values[] = $otherCount;
            }
        }

        if (empty($values)) {
            $names = ['Aucune donnée'];
            $values = [1];
        }

        return [
            'names' => $names,
            'values' => $values,
            'colors' => array_slice($palette, 0, count($values)),
        ];
    }

    public function getMissionsTrendChart(): array
    {
        $months = [];
        $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            $values[] = Mission::whereYear('date_start', $date->year)
                ->whereMonth('date_start', $date->month)
                ->whereIn('status', [Mission::STATUS_APPROVED, Mission::STATUS_COMPLETED])
                ->count();
        }

        return [
            'labels' => $months,
            'values' => $values,
        ];
    }

    public function getSinistresTrendChart(): array
    {
        $months = [];
        $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            $values[] = Sinistre::whereYear('declared_at', $date->year)
                ->whereMonth('declared_at', $date->month)
                ->count();
        }

        return [
            'labels' => $months,
            'values' => $values,
        ];
    }

    public function getRepairsCostTrendChart(): array
{
    $months = [];
    $internal = [];
    $external = [];

    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $months[] = $date->translatedFormat('M Y');

        $base = Repair::whereYear('completed_at', $date->year)
            ->whereMonth('completed_at', $date->month)
            ->whereNotNull('completed_at');

        $internal[] = round((float) (clone $base)->where('type', Repair::TYPE_INTERNAL)->sum('cost'), 0);
        $external[] = round((float) (clone $base)->where('type', Repair::TYPE_EXTERNAL)->sum('cost'), 0);
    }

    return [
        'labels'   => $months,
        'internal' => $internal,
        'external' => $external,
    ];
}

    public function getRecentActivity(): \Illuminate\Support\Collection
    {
        return AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get()
            ->map(function (AuditLog $log) {
                $desc = $log->description ?? $log->action_label;
                if ($log->auditable_type) {
                    $type = class_basename($log->auditable_type);
                    if ($type === 'Vehicle' && isset($log->new_values['registration'])) {
    $desc = 'Véhicule ' . $log->new_values['registration'] . ' — ' . $log->action_label;}
                     elseif ($type === 'Sinistre') {
                        $desc = 'Sinistre — ' . $log->action_label;
                    } 
                    elseif ($type === 'Mission') {
                        $desc = 'Mission — ' . $log->action_label;
                    }
                }
                return [
                    'description' => $desc,
                    'time_ago' => $log->created_at->diffForHumans(),
                    'action' => $log->action,
                    'created_at' => $log->created_at,
                ];
            });
    }

    public function getInsights(): array
    {
        $list = [];
        $kpis = $this->getKpis();

        if ($kpis['availability_rate'] < 50 && $kpis['total_vehicles'] > 0) {
            $list[] = [
                'type' => 'warning',
                'icon' => 'bi bi-speedometer2',
                'title' => 'Taux de disponibilité faible',
                'text' => "Seulement {$kpis['availability_rate']}% du parc est disponible. Vérifiez les affectations et réparations.",
            ];
        }

        if ($kpis['insurances_expiring_30'] > 0) {
            $list[] = [
                'type' => 'info',
                'icon' => 'bi bi-shield-exclamation',
                'title' => 'Assurances à renouveler',
                'text' => $kpis['insurances_expiring_30'] . ' contrat(s) expire(nt) dans les 30 prochains jours.',
            ];
        }

        if ($kpis['sinistres_open'] > 0) {
            $list[] = [
                'type' => 'danger',
                'icon' => 'bi bi-exclamation-triangle',
                'title' => 'Sinistres en cours',
                'text' => $kpis['sinistres_open'] . ' sinistre(s) à traiter ou en réparation.',
            ];
        }

        if ($kpis['repairs_ongoing'] > 0) {
            $list[] = [
                'type' => 'warning',
                'icon' => 'bi bi-wrench',
                'title' => 'Réparations en cours',
                'text' => $kpis['repairs_ongoing'] . ' réparation(s) non clôturée(s).',
            ];
        }

        $vehiclesWithoutInsurance = Vehicle::whereNull('insurance_contract_global_id')->count();
        if ($vehiclesWithoutInsurance > 0) {
            $list[] = [
                'type' => 'danger',
                'icon' => 'bi bi-shield-x',
                'title' => 'Véhicules sans assurance',
                'text' => $vehiclesWithoutInsurance . ' véhicule(s) sans contrat d\'assurance renseigné.',
            ];
        }

        if ($kpis['missions_this_month'] > 0) {
            $list[] = [
                'type' => 'success',
                'icon' => 'bi bi-calendar-check',
                'title' => 'Activité missions',
                'text' => $kpis['missions_this_month'] . ' mission(s) ce mois-ci.',
            ];
        }

        if (empty($list)) {
            $list[] = [
                'type' => 'success',
                'icon' => 'bi bi-check-circle',
                'title' => 'Tout va bien',
                'text' => 'Aucun point d\'attention majeur. Taux de disponibilité : ' . $kpis['availability_rate'] . '%.',
            ];
        }

        return $list;
    }

    public function getQuickStats(): array
    {
        $totalMissionsKm = Mission::whereIn('status', [Mission::STATUS_COMPLETED])
           // ->whereMonth('date_end', now()->month)
           // ->whereYear('date_end', now()->year)
            ->sum('distance_km');

        $baseRepairs = Repair::whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->whereNotNull('completed_at');

        $repairCostInternal = (clone $baseRepairs)->where('type', Repair::TYPE_INTERNAL)->sum('cost');
        $repairCostExternal = (clone $baseRepairs)->where('type', Repair::TYPE_EXTERNAL)->sum('cost');

        return [
            'km_this_month' => (int) $totalMissionsKm,
            'repair_cost_this_month' => round((float) $repairCostInternal + (float) $repairCostExternal, 0),
            'repair_cost_internal_this_month' => round((float) $repairCostInternal, 0),
            'repair_cost_external_this_month' => round((float) $repairCostExternal, 0),
        ];
    }


    /** Statistiques par véhicule (missions + km) */
    public function getDriverTripStats(string $period = 'month'): \Illuminate\Support\Collection
{
    return Mission::query()
        ->whereIn('status', [Mission::STATUS_APPROVED, Mission::STATUS_COMPLETED])
        ->whereNotNull('driver_id')
        ->when($period === 'month', fn($q) =>
            $q->whereMonth('date_start', now()->month)->whereYear('date_start', now()->year)
        )
        ->when($period === 'quarter', fn($q) =>
            $q->whereBetween('date_start', [now()->firstOfQuarter(), now()->lastOfQuarter()])
        )
        ->when($period === 'year', fn($q) =>
            $q->whereYear('date_start', now()->year)
        )
        ->selectRaw('
            driver_id,
            count(*) as missions_count,
            coalesce(sum(distance_km), 0) as total_km,
            coalesce(avg(distance_km), 0) as avg_km,
            avg(TIMESTAMPDIFF(MINUTE, date_start, date_end)) as avg_duration_minutes,
            max(date_start) as last_mission
        ')
        ->groupBy('driver_id')
        ->orderByDesc('missions_count')
        ->limit(10)
        ->get()
        ->map(function ($row) {
            $driver = Driver::find($row->driver_id);
            return [
                'name'               => $driver ? $driver->full_name : '—',
                'missions_count'     => (int) $row->missions_count,
                'total_km'           => (int) $row->total_km,
                'avg_km'             => round((float) $row->avg_km, 1),
                'avg_duration_min'   => $row->avg_duration_minutes ? (int) $row->avg_duration_minutes : null,
                'last_mission'       => $row->last_mission
                    ? \Carbon\Carbon::parse($row->last_mission)->translatedFormat('d M Y')
                    : '—',
            ];
        });
}

public function getVehicleTripStats(string $period = 'month'): \Illuminate\Support\Collection
{
    return Mission::query()
        ->whereIn('status', [Mission::STATUS_APPROVED, Mission::STATUS_COMPLETED])
        ->whereNotNull('vehicle_id')
        ->when($period === 'month', fn($q) =>
            $q->whereMonth('date_start', now()->month)->whereYear('date_start', now()->year)
        )
        ->when($period === 'quarter', fn($q) =>
            $q->whereBetween('date_start', [now()->firstOfQuarter(), now()->lastOfQuarter()])
        )
        ->when($period === 'year', fn($q) =>
            $q->whereYear('date_start', now()->year)
        )
        ->selectRaw('
            vehicle_id,
            count(*) as missions_count,
            coalesce(sum(distance_km), 0) as total_km,
            coalesce(avg(distance_km), 0) as avg_km,
            avg(TIMESTAMPDIFF(MINUTE, date_start, date_end)) as avg_duration_minutes,
            max(date_start) as last_mission
        ')
        ->groupBy('vehicle_id')
        ->orderByDesc('missions_count')
        ->limit(10)
        ->get()
        ->map(function ($row) {
            $vehicle = Vehicle::find($row->vehicle_id);
            return [
                'name'               => $vehicle ? $vehicle->registration : '—',
                'missions_count'     => (int) $row->missions_count,
                'total_km'           => (int) $row->total_km,
                'avg_km'             => round((float) $row->avg_km, 1),
                'avg_duration_min'   => $row->avg_duration_minutes ? (int) $row->avg_duration_minutes : null,
                'last_mission'       => $row->last_mission
                    ? \Carbon\Carbon::parse($row->last_mission)->translatedFormat('d M Y')
                    : '—',
            ];
        });
}


    public function render()
    {
        $kpis = $this->getKpis();
        $activity = $this->getRecentActivity();
        $insights = $this->getInsights();
        $quickStats = $this->getQuickStats();

        $chartStatus = $this->getVehiclesByStatusChart();
        $chartCategory = $this->getVehiclesByCategoryChart();
        $chartMissions = $this->getMissionsTrendChart();
        $chartSinistres = $this->getSinistresTrendChart();
        $chartRepairs = $this->getRepairsCostTrendChart();
        $driverTripStats = $this->getDriverTripStats();
        $vehicleTripStats = $this->getVehicleTripStats();
        $driverTripStats  = $this->getDriverTripStats($this->tripPeriod);
        $vehicleTripStats = $this->getVehicleTripStats($this->tripPeriod);

        return view('livewire.portal.dashboard.index', [
            'kpis' => $kpis,
            'activity' => $activity,
            'insights' => $insights,
            'quickStats' => $quickStats,
            'chartStatus' => $chartStatus,
            'chartCategory' => $chartCategory,
            'chartMissions' => $chartMissions,
            'chartSinistres' => $chartSinistres,
            'chartRepairs' => $chartRepairs,
            'driverTripStats' => $driverTripStats,
            'vehicleTripStats' => $vehicleTripStats,
            
        ])->layout('layouts.app', ['title' => 'Tableau de bord']);
    }
}
