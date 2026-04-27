<?php

namespace App\Livewire\Portal\Missions;

use App\Models\Direction;
use App\Models\Driver;
use App\Models\Mission;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Analytics extends Component
{
    public string $start_date = '';
    public string $end_date = '';
    public ?int $driver_id = null;
    public ?int $direction_id = null;
    public string $status = '';

    public function mount(): void
    {
        $this->start_date = now()->startOfMonth()->toDateString();
        $this->end_date = now()->endOfMonth()->toDateString();
    }

    public function getStatsProperty(): array
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->endOfDay();

        $query = Mission::query()
            ->whereBetween('date_start', [$start, $end])
            ->with(['driver', 'demandeur.direction', 'technicians']);

        if ($this->driver_id) {
            $query->where('driver_id', $this->driver_id);
        }
        if ($this->direction_id) {
            $query->whereHas('demandeur', fn ($q) => $q->where('direction_id', $this->direction_id));
        }
        if ($this->status !== '') {
            if ($this->status === Mission::STATUS_PROGRAMMED) {
                $query->whereIn('status', [Mission::STATUS_PROGRAMMED, Mission::STATUS_APPROVED]);
            } else {
                $query->where('status', $this->status);
            }
        }

        $missions = $query->get();

        $countByStatus = [
            'programmed' => $missions->whereIn('status', [Mission::STATUS_PROGRAMMED, Mission::STATUS_APPROVED])->count(),
            'in_progress' => $missions->where('status', Mission::STATUS_IN_PROGRESS)->count(),
            'postponed' => $missions->where('status', Mission::STATUS_POSTPONED)->count(),
            'completed' => $missions->where('status', Mission::STATUS_COMPLETED)->count(),
            'pending' => $missions->where('status', Mission::STATUS_PENDING)->count(),
        ];

        $topDrivers = $missions
            ->groupBy('driver_id')
            ->map(function ($items) {
                $driver = $items->first()?->driver;
                return [
                    'name' => $driver ? $driver->full_name : 'Non affecté',
                    'missions' => $items->count(),
                    'distance' => (int) $items->sum('distance_km'),
                ];
            })
            ->sortByDesc('missions')
            ->take(5)
            ->values();

        $topDirections = $missions
            ->groupBy(fn ($m) => $m->demandeur?->direction?->name ?? 'Non renseignée')
            ->map(fn ($items, $name) => [
                'name' => $name,
                'missions' => $items->count(),
            ])
            ->sortByDesc('missions')
            ->take(5)
            ->values();

        $topTechnicians = $missions
            ->flatMap(function ($mission) {
                return $mission->technicians->map(function ($tech) use ($mission) {
                    return [
                        'id' => $tech->id,
                        'name' => trim(($tech->last_name ?? '') . ' ' . ($tech->first_name ?? '')),
                        'distance' => (int) ($mission->distance_km ?? 0),
                    ];
                });
            })
            ->groupBy('id')
            ->map(fn ($items) => [
                'name' => $items->first()['name'] ?: 'Technicien',
                'missions' => $items->count(),
                'distance' => (int) $items->sum('distance'),
            ])
            ->sortByDesc('missions')
            ->take(5)
            ->values();

        return [
            'total' => $missions->count(),
            'distance' => (int) $missions->sum('distance_km'),
            'by_status' => $countByStatus,
            'top_drivers' => $topDrivers,
            'top_directions' => $topDirections,
            'top_technicians' => $topTechnicians,
        ];
    }

    public function render(): View
    {
        return view('livewire.portal.missions.analytics', [
            'stats' => $this->stats,
            'drivers' => Driver::orderBy('last_name')->get(['id', 'first_name', 'last_name']),
            'directions' => Direction::orderBy('name')->get(['id', 'name']),
            'statusOptions' => Mission::statusOptions(),
        ])->layout('layouts.app', ['title' => 'Analyses déplacements']);
    }
}

