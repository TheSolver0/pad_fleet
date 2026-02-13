<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DiagnosticPDFController extends Controller
{
    /**
     * Générer le PDF d'un diagnostic
     */
    public function generatePDF(int $id)
    {
        $diagnostic = Diagnostic::with(['vehicle', 'mechanic'])->findOrFail($id);
        
        // Pour l'instant, retourner une vue HTML simple
        // TODO: Intégrer domPDF ou autre librairie PDF
        $html = view('pdf.diagnostic', [
            'diagnostic' => $diagnostic
        ])->render();
        
        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'inline; filename="diagnostic-' . $diagnostic->reference . '.html"'
        ]);
    }
}
