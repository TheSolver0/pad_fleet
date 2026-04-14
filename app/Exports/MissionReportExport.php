<?php

namespace App\Exports;

use App\Models\Mission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MissionReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private ?string $startDate = null,
        private ?string $endDate = null,
        private ?string $status = null,
        private ?int $demandeurId = null
    ) {}

    public function query()
    {
        $query = Mission::query()
            ->with(['vehicle:id,registration', 'driver:id,first_name,last_name', 'demandeur.service:id,name', 'city:id,name,region'])
            ->orderBy('date_start', 'desc');

        if ($this->startDate) {
            $query->where('date_start', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('date_end', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->demandeurId) {
            $query->where('demandeur_id', $this->demandeurId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Mission',
            'Statut',
            'Demandeur',
            'Service/Direction',
            'Chauffeur',
            'Véhicule',
            'Date départ',
            'Date retour',
            'Nb Jours',
            'Destination',
            'Ville',
            'Région',
            'Raison du déplacement',
            'KM départ',
            'KM retour',
            'Distance (km)',
            'Notes',
            'Date création',
        ];
    }

    public function map($mission): array
    {
        return [
            $mission->id,
            $mission->status_label,
            $mission->demandeur?->name ?? '',
            $mission->demandeur?->service?->name ?? '',
            $mission->driver ? $mission->driver->full_name : '',
            $mission->vehicle?->registration ?? '',
            $mission->date_start?->format('d/m/Y H:i'),
            $mission->date_end?->format('d/m/Y H:i'),
            $mission->date_start && $mission->date_end
                ? max(1, (int) $mission->date_start->diffInDays($mission->date_end) + 1)
                : '',
            $mission->destination ?? '',
            $mission->city?->name ?? '',
            $mission->city?->region ?? '',
            $mission->raison ?? '',
            $mission->km_departure ?? '',
            $mission->km_return ?? '',
            $mission->distance_km ?? '',
            $mission->notes ?? '',
            $mission->created_at?->format('d/m/Y H:i'),
        ];
    }
}