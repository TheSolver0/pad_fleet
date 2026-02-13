<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class WorkOrderPDFController extends Controller
{
    /**
     * Générer le PDF d'un bon de travail
     */
    public function generatePDF(int $id)
    {
        $workOrder = WorkOrder::with(['vehicle', 'mechanic', 'diagnostic'])->findOrFail($id);
        
        // Pour l'instant, retourner une vue HTML simple
        // TODO: Intégrer domPDF ou autre librairie PDF
        $html = view('pdf.work-order', [
            'workOrder' => $workOrder
        ])->render();
        
        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'inline; filename="bon-de-travail-' . $workOrder->reference . '.html"'
        ]);
    }
}
