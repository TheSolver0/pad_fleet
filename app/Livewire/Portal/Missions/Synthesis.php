<?php

namespace App\Livewire\Portal\Missions;

use App\Models\Mission;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Synthesis extends Component
{
    public string $period_start = '';
    public string $period_end   = '';

    public function mount(): void
    {
        // Par défaut : trimestre en cours
        $this->period_start = now()->firstOfQuarter()->format('Y-m-d');
        $this->period_end   = now()->lastOfQuarter()->format('Y-m-d');
    }

    public function setPeriod(string $preset): void
    {
        match ($preset) {
            'month'   => [$this->period_start, $this->period_end] = [
                now()->firstOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d'),
            ],
            'quarter' => [$this->period_start, $this->period_end] = [
                now()->firstOfQuarter()->format('Y-m-d'),
                now()->lastOfQuarter()->format('Y-m-d'),
            ],
            'year'    => [$this->period_start, $this->period_end] = [
                now()->startOfYear()->format('Y-m-d'),
                now()->endOfYear()->format('Y-m-d'),
            ],
            default => null,
        };
    }

    // ──────────────────────────────────────────────────────────────
    // Données de synthèse
    // ──────────────────────────────────────────────────────────────

    private function baseQuery()
    {
        $q = Mission::query()
            ->with(['vehicle:id,registration', 'driver:id,first_name,last_name', 'demandeur:id,name', 'city:id,name,region'])
            ->whereNotIn('status', [Mission::STATUS_REJECTED, Mission::STATUS_CANCELLED]);

        if ($this->period_start) {
            $q->where('date_start', '>=', $this->period_start . ' 00:00:00');
        }
        if ($this->period_end) {
            $q->where('date_start', '<=', $this->period_end . ' 23:59:59');
        }

        return $q;
    }

    public function getSynthesisData(): array
    {
        $missions = $this->baseQuery()->orderBy('date_start')->get();
        $total = $missions->count();

        if ($total === 0) {
            return ['total' => 0, 'missions' => collect()];
        }

        // Durée par mission en jours (au moins 1)
        $missions = $missions->map(function ($m) {
            $days = max(1, (int) $m->date_start->diffInDays($m->date_end) + 1);
            $m->nb_jours = $days;
            return $m;
        });

        $totalJours  = $missions->sum('nb_jours');
        $avgDuration = $total > 0 ? round($totalJours / $total, 1) : 0;

        // ── Par demandeur / direction ──
        $byDemandeur = $missions
            ->groupBy(fn($m) => $m->demandeur?->name ?? 'Non défini')
            ->map(fn($g) => $g->count())
            ->sortDesc();

        // ── Par destination (ville ou champ destination) ──
        $byDestination = $missions
            ->groupBy(function ($m) {
                return $m->city?->name ?? ($m->destination ? strtoupper(trim($m->destination)) : 'Non défini');
            })
            ->map(fn($g) => $g->count())
            ->sortDesc();

        $topDestination = $byDestination->first() ? [
            'name'    => $byDestination->keys()->first(),
            'count'   => $byDestination->first(),
            'percent' => $total > 0 ? round($byDestination->first() / $total * 100) : 0,
        ] : null;

        // ── Par raison/motif ──
        $byRaison = $missions
            ->filter(fn($m) => $m->raison)
            ->groupBy(fn($m) => $m->raison)
            ->map(fn($g) => $g->count())
            ->sortDesc();

        // ── Couverture géographique (par région) ──
        $byRegion = $missions
            ->filter(fn($m) => $m->city?->region)
            ->groupBy(fn($m) => $m->city->region)
            ->map(function ($g) {
                return [
                    'count' => $g->count(),
                    'villes' => $g->map(fn($m) => $m->city?->name)->unique()->filter()->values()->toArray(),
                ];
            });

        return [
            'total'          => $total,
            'total_jours'    => $totalJours,
            'avg_duration'   => $avgDuration,
            'top_destination'=> $topDestination,
            'by_demandeur'   => $byDemandeur,
            'by_destination' => $byDestination->take(10),
            'by_raison'      => $byRaison,
            'by_region'      => $byRegion,
            'missions'       => $missions,
        ];
    }

    public function render(): View
    {
        $data = $this->getSynthesisData();

        return view('livewire.portal.missions.synthesis', [
            'data'         => $data,
            'period_start' => $this->period_start,
            'period_end'   => $this->period_end,
        ])->layout('layouts.app', ['title' => 'Synthèse des déplacements']);
    }
}
