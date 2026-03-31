<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehicleConsumptionExport implements FromArray, WithHeadings
{
    public function __construct(private array $rows)
    {
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
}
