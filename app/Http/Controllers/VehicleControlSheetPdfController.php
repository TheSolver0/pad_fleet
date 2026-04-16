<?php

namespace App\Http\Controllers;

use App\Models\VehicleControlSheet;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleControlSheetPdfController extends Controller
{
    public function __invoke(int $id)
    {
        $sheet = VehicleControlSheet::with(['vehicle', 'driver', 'mission', 'photos'])
            ->findOrFail($id);

        $labels = VehicleControlSheet::labels();

        $pdf = Pdf::loadView('pdf.vehicle-control-sheet', compact('sheet', 'labels'))
            ->setPaper('a4', 'portrait');

        $filename = 'fiche-controle-' . ($sheet->vehicle?->registration ?? $sheet->id) . '-' . ($sheet->date_depart?->format('Ymd') ?? date('Ymd')) . '.pdf';

        return $pdf->stream($filename);
    }
}
