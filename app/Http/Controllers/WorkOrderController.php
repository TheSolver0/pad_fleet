<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\WorkOrder;
use App\Models\Vehicle;
use App\Models\Mechanic;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WorkOrderController extends Controller
{
    /**
     * Afficher le formulaire de création de bon de travail
     */
    public function create(Request $request): View
    {
        $diagnosticId = $request->get('diagnostic_id');
        $diagnostic = null;
        
        if ($diagnosticId) {
            $diagnostic = Diagnostic::findOrFail($diagnosticId);
        }

        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);
        $mechanics = Mechanic::where('is_active', true)->orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $diagnostics = Diagnostic::orderByDesc('diagnostic_date')->get(['id', 'reference', 'vehicle_id']);

        return view('work-orders.create', [
            'diagnostic' => $diagnostic,
            'vehicles' => $vehicles,
            'mechanics' => $mechanics,
            'diagnostics' => $diagnostics,
        ]);
    }
}
