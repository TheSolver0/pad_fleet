<?php

namespace App\Exports;

use App\Exports\Concerns\WithPadLetterhead;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehicleConsumptionExport implements FromArray, WithHeadings, WithEvents
{
    use WithPadLetterhead;

    public function __construct(private array $rows)
    {
        $this->letterheadTitle = 'Rapport de consommation des véhicules';
    }

    public function headings(): array
    {
        return [
            'Categorie',
            'Piece',
            'Reference',
            'Quantite totale',
            'Cout total (F)',
            'Nombre utilisations',
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    protected function letterheadColumnCount(): int
    {
        return count($this->headings());
    }

    public function registerEvents(): array
    {
        return $this->letterheadEvents();
    }
}
