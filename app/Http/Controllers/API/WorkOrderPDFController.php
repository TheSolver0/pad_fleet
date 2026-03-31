<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkOrderPDFController extends Controller
{
    /**
     * Générer le PDF d'un bon de travail
     */
    public function generatePDF(int $id)
    {
        $workOrder = WorkOrder::with(['vehicle', 'mechanic', 'diagnostic'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.work-order', [
            'workOrder' => $workOrder,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('bon-de-travail-' . $workOrder->reference . '.pdf');
    }

    public function generateTransferPDF(int $id)
    {
        $workOrder = WorkOrder::with(['vehicle', 'mechanic', 'diagnostic', 'parts.article'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.transfer-sheet', [
            'workOrder' => $workOrder,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('fiche-transfert-' . $workOrder->reference . '.pdf');
    }
}
