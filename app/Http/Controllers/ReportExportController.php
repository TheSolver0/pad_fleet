<?php

namespace App\Http\Controllers;

use App\Exports\TableExport;
use App\Exports\VehicleConsumptionExport;
use App\Models\Driver;
use App\Models\Mission;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\Sinistre;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    private function getPeriod(Request $request): array
    {
        $startRaw = $request->string('start_date')->toString() ?: now()->startOfMonth()->toDateString();
        $endRaw = $request->string('end_date')->toString() ?: now()->toDateString();

        $start = Carbon::parse($startRaw)->startOfDay();
        $end = Carbon::parse($endRaw)->endOfDay();

        return [$start, $end, $start->toDateString(), $end->toDateString()];
    }

    private function getTopParts(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);
        $category = $request->string('vehicle_category')->toString();

        return RepairPart::query()
            ->whereHas('repair', fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->whereHas('repair.vehicle', function ($q) use ($category) {
                if ($category !== '') {
                    $q->where('category', $category);
                }
            })
            ->with('article')
            ->get()
            ->groupBy(fn ($p) => $p->article?->name ?? 'N/A')
            ->map(function ($group) use ($category) {
                $first = $group->first();
                return [
                    'category' => $category !== '' ? $category : 'Toutes',
                    'name' => $first->article?->name ?? 'N/A',
                    'reference' => $first->article?->reference ?? '-',
                    'total_quantity' => (int) $group->sum('quantity_used'),
                    'total_cost' => (float) $group->sum('total_price'),
                    'usage_count' => (int) $group->count(),
                ];
            })
            ->sortByDesc('total_cost')
            ->take(200)
            ->values();
    }

    private function getMissionAnalyticsData(Request $request): array
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);
        $driverId = (int) $request->query('driver_id', 0);
        $directionId = (int) $request->query('direction_id', 0);
        $status = trim((string) $request->query('status', ''));

        $query = Mission::query()
            ->whereBetween('date_start', [$start, $end])
            ->with(['driver:id,first_name,last_name', 'demandeur:id,name,direction_id', 'demandeur.direction:id,name']);

        if ($driverId > 0) {
            $query->where('driver_id', $driverId);
        }
        if ($directionId > 0) {
            $query->whereHas('demandeur', fn ($q) => $q->where('direction_id', $directionId));
        }
        if ($status !== '') {
            if ($status === Mission::STATUS_PROGRAMMED) {
                $query->whereIn('status', [Mission::STATUS_PROGRAMMED, Mission::STATUS_APPROVED]);
            } else {
                $query->where('status', $status);
            }
        }

        $missions = $query->orderByDesc('date_start')->get();

        $rows = $missions->map(fn ($m) => [
            optional($m->date_start)?->format('d/m/Y H:i'),
            optional($m->date_end)?->format('d/m/Y H:i'),
            trim(($m->driver?->first_name ?? '') . ' ' . ($m->driver?->last_name ?? '')) ?: 'Non affecté',
            $m->demandeur?->direction?->name ?? 'Non renseignée',
            $m->destination ?? '-',
            (int) ($m->distance_km ?? 0),
            $m->status_label,
        ])->all();

        $stats = [
            'total' => $missions->count(),
            'distance' => (int) $missions->sum('distance_km'),
            'programmed' => $missions->whereIn('status', [Mission::STATUS_PROGRAMMED, Mission::STATUS_APPROVED])->count(),
            'in_progress' => $missions->where('status', Mission::STATUS_IN_PROGRESS)->count(),
            'postponed' => $missions->where('status', Mission::STATUS_POSTPONED)->count(),
            'completed' => $missions->where('status', Mission::STATUS_COMPLETED)->count(),
            'pending' => $missions->where('status', Mission::STATUS_PENDING)->count(),
        ];

        return [$rows, $stats, $startDate, $endDate];
    }

    public function vehicleConsumptionExcel(Request $request)
    {
        $rows = $this->getTopParts($request)->map(fn ($r) => [
            $r['category'],
            $r['name'],
            $r['reference'],
            $r['total_quantity'],
            $r['total_cost'],
            $r['usage_count'],
        ])->all();

        return Excel::download(new VehicleConsumptionExport($rows), 'rapport-consommation-vehicules.xlsx');
    }

    public function vehicleConsumptionPdf(Request $request)
    {
        $parts = $this->getTopParts($request);
        [, , $startDate, $endDate] = $this->getPeriod($request);

        $pdf = Pdf::loadView('pdf.vehicle-consumption', [
            'parts' => $parts,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'vehicle_category' => $request->string('vehicle_category')->toString(),
            'generated_at' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('rapport-consommation-vehicules.pdf');
    }

    public function fleetGlobalPdf(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);
        $category = $request->string('vehicle_category')->toString();

        $missions = Mission::query()
            ->whereBetween('date_start', [$start, $end])
            ->whereIn('status', [Mission::STATUS_APPROVED, Mission::STATUS_COMPLETED])
            ->get();
        $repairs = Repair::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($category !== '', fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('category', $category)))
            ->get();
        $sinistres = Sinistre::query()->whereBetween('declared_at', [$start, $end])->get();
        $parts = $this->getTopParts($request);

        $stockMovements = StockMovement::query()->whereBetween('created_at', [$start, $end])->get();
        $stockEntries = $stockMovements->where('type', StockMovement::TYPE_ENTRY);
        $stockExits = $stockMovements->where('type', StockMovement::TYPE_EXIT);

        $stats = [
            'missions_count' => $missions->count(),
            'missions_distance' => (int) $missions->sum('distance_km'),
            'repairs_count' => $repairs->count(),
            'repairs_cost' => (float) $repairs->sum('cost'),
            'sinistres_count' => $sinistres->count(),
            'sinistres_estimated_cost' => (float) $sinistres->sum('estimated_cost'),
            'parts_quantity' => (int) $parts->sum('total_quantity'),
            'parts_cost' => (float) $parts->sum('total_cost'),
            'stock_entries_qty' => (int) $stockEntries->sum('quantity'),
            'stock_entries_cost' => (float) $stockEntries->sum('total_cost'),
            'stock_exits_qty' => (int) $stockExits->sum('quantity'),
            'stock_exits_cost' => (float) $stockExits->sum('total_cost'),
        ];

        $pdf = Pdf::loadView('pdf.fleet-executive-summary', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'vehicle_category' => $category,
            'generated_at' => now(),
            'stats' => $stats,
            'top_parts' => $parts->take(15),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("synthese-executive-{$startDate}-{$endDate}.pdf");
    }

    public function stockMovementsExcel(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $movements = StockMovement::query()
            ->whereBetween('created_at', [$start, $end])
            ->with('article:id,name,reference')
            ->orderByDesc('created_at')
            ->get();

        $rows = $movements->map(fn ($sm) => [
            optional($sm->created_at)?->format('d/m/Y H:i'),
            $sm->type_label,
            $sm->location_label,
            $sm->article?->name ?? 'N/A',
            $sm->article?->reference ?? '-',
            (int) ($sm->quantity ?? 0),
            (float) ($sm->unit_price ?? 0),
            (float) ($sm->total_cost ?? 0),
            $sm->reference ?? '-',
            $sm->reason ?? '-',
        ])->all();

        return Excel::download(
            new TableExport(['Date', 'Type', 'Magasin', 'Article', 'Ref', 'Qte', 'PU (F)', 'Total (F)', 'Reference', 'Motif'], $rows),
            "rapport-stock-entrees-sorties-{$startDate}-{$endDate}.xlsx"
        );
    }

    public function stockMovementsPdf(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $movements = StockMovement::query()
            ->whereBetween('created_at', [$start, $end])
            ->with('article:id,name,reference')
            ->orderByDesc('created_at')
            ->get();

        $headers = ['Date', 'Type', 'Magasin', 'Article', 'Ref', 'Qte', 'PU (F)', 'Total (F)'];
        $rows = $movements->map(fn ($sm) => [
            optional($sm->created_at)?->format('d/m/Y H:i'),
            $sm->type_label,
            $sm->location_label,
            $sm->article?->name ?? 'N/A',
            $sm->article?->reference ?? '-',
            (int) ($sm->quantity ?? 0),
            number_format((float) ($sm->unit_price ?? 0), 0, ',', ' '),
            number_format((float) ($sm->total_cost ?? 0), 0, ',', ' '),
        ])->all();

        $pdf = Pdf::loadView('pdf.table-report', [
            'title' => 'Rapport gestion des stocks (entrées / sorties)',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-stock-entrees-sorties-{$startDate}-{$endDate}.pdf");
    }

    public function stockPerVehicleExcel(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);
        $category = $request->string('vehicle_category')->toString();

        $data = DB::table('repair_parts')
            ->join('repairs', 'repairs.id', '=', 'repair_parts.repair_id')
            ->join('vehicles', 'vehicles.id', '=', 'repairs.vehicle_id')
            ->leftJoin('articles', 'articles.id', '=', 'repair_parts.article_id')
            ->whereBetween('repairs.created_at', [$start, $end])
            ->when($category !== '', fn ($q) => $q->where('vehicles.category', $category))
            ->groupBy('vehicles.id', 'vehicles.registration', 'vehicles.category', 'articles.name', 'articles.reference')
            ->selectRaw('vehicles.registration as registration')
            ->selectRaw('vehicles.category as category')
            ->selectRaw('COALESCE(articles.name, "N/A") as article_name')
            ->selectRaw('COALESCE(articles.reference, "-") as article_reference')
            ->selectRaw('SUM(repair_parts.quantity_used) as total_quantity')
            ->selectRaw('SUM(repair_parts.total_price) as total_cost')
            ->orderByRaw('SUM(repair_parts.total_price) DESC')
            ->get();

        $rows = $data->map(fn ($r) => [
            $r->registration,
            $r->category,
            $r->article_name,
            $r->article_reference,
            (int) $r->total_quantity,
            (float) $r->total_cost,
        ])->all();

        return Excel::download(
            new TableExport(['Vehicule', 'Categorie', 'Piece', 'Reference', 'Quantite consommee', 'Cout total (F)'], $rows),
            "rapport-consommation-par-vehicule-{$startDate}-{$endDate}.xlsx"
        );
    }

    public function stockPerVehiclePdf(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);
        $category = $request->string('vehicle_category')->toString();

        $data = DB::table('repair_parts')
            ->join('repairs', 'repairs.id', '=', 'repair_parts.repair_id')
            ->join('vehicles', 'vehicles.id', '=', 'repairs.vehicle_id')
            ->leftJoin('articles', 'articles.id', '=', 'repair_parts.article_id')
            ->whereBetween('repairs.created_at', [$start, $end])
            ->when($category !== '', fn ($q) => $q->where('vehicles.category', $category))
            ->groupBy('vehicles.id', 'vehicles.registration', 'vehicles.category', 'articles.name', 'articles.reference')
            ->selectRaw('vehicles.registration as registration')
            ->selectRaw('vehicles.category as category')
            ->selectRaw('COALESCE(articles.name, "N/A") as article_name')
            ->selectRaw('COALESCE(articles.reference, "-") as article_reference')
            ->selectRaw('SUM(repair_parts.quantity_used) as total_quantity')
            ->selectRaw('SUM(repair_parts.total_price) as total_cost')
            ->orderByRaw('SUM(repair_parts.total_price) DESC')
            ->get();

        $headers = ['Véhicule', 'Catégorie', 'Pièce', 'Référence', 'Quantité', 'Coût total (F)'];
        $rows = $data->map(fn ($r) => [
            $r->registration,
            $r->category,
            $r->article_name,
            $r->article_reference,
            (int) $r->total_quantity,
            number_format((float) $r->total_cost, 0, ',', ' '),
        ])->all();

        $pdf = Pdf::loadView('pdf.table-report', [
            'title' => 'Rapport consommation stock par véhicule',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'vehicle_category' => $category,
            'generated_at' => now(),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-consommation-par-vehicule-{$startDate}-{$endDate}.pdf");
    }

    public function driversExcel(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $drivers = Driver::query()
            ->withCount([
                'missions as missions_completed_count' => fn ($q) => $q->whereBetween('date_start', [$start, $end])->where('status', Mission::STATUS_COMPLETED),
            ])
            ->withSum([
                'missions as missions_distance_km_sum' => fn ($q) => $q->whereBetween('date_start', [$start, $end])->where('status', Mission::STATUS_COMPLETED),
            ], 'distance_km')
            ->get();

        $rows = $drivers->map(function ($d) use ($start, $end) {
            $sinistresCount = Sinistre::query()
                ->where('driver_id', $d->id)
                ->whereBetween('declared_at', [$start, $end])
                ->count();

            return [
                $d->matricule ?? '-',
                trim(($d->first_name ?? '') . ' ' . ($d->last_name ?? '')),
                $d->phone ?? '-',
                $d->email ?? '-',
                (int) ($d->missions_completed_count ?? 0),
                (int) ($d->missions_distance_km_sum ?? 0),
                (int) $sinistresCount,
                $d->is_available ? 'Disponible' : 'Indisponible',
            ];
        })->all();

        return Excel::download(
            new TableExport(['Matricule', 'Chauffeur', 'Telephone', 'Email', 'Missions terminees', 'Distance (km)', 'Sinistres', 'Disponibilite'], $rows),
            "rapport-chauffeurs-{$startDate}-{$endDate}.xlsx"
        );
    }

    public function driversPdf(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $drivers = Driver::query()
            ->withCount([
                'missions as missions_completed_count' => fn ($q) => $q->whereBetween('date_start', [$start, $end])->where('status', Mission::STATUS_COMPLETED),
            ])
            ->withSum([
                'missions as missions_distance_km_sum' => fn ($q) => $q->whereBetween('date_start', [$start, $end])->where('status', Mission::STATUS_COMPLETED),
            ], 'distance_km')
            ->get();

        $headers = ['Matricule', 'Chauffeur', 'Téléphone', 'Email', 'Missions', 'Distance (km)', 'Sinistres', 'Disponibilité'];
        $rows = $drivers->map(function ($d) use ($start, $end) {
            $sinistresCount = Sinistre::query()
                ->where('driver_id', $d->id)
                ->whereBetween('declared_at', [$start, $end])
                ->count();

            return [
                $d->matricule ?? '-',
                trim(($d->first_name ?? '') . ' ' . ($d->last_name ?? '')),
                $d->phone ?? '-',
                $d->email ?? '-',
                (int) ($d->missions_completed_count ?? 0),
                (int) ($d->missions_distance_km_sum ?? 0),
                (int) $sinistresCount,
                $d->is_available ? 'Disponible' : 'Indisponible',
            ];
        })->all();

        $pdf = Pdf::loadView('pdf.table-report', [
            'title' => 'Rapport gestion des chauffeurs',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-chauffeurs-{$startDate}-{$endDate}.pdf");
    }

    public function repairsExcel(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);
        $category = $request->string('vehicle_category')->toString();

        $repairs = Repair::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($category !== '', fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('category', $category)))
            ->with(['vehicle:id,registration', 'mechanic:id,first_name,last_name'])
            ->orderByDesc('created_at')
            ->get();

        $rows = $repairs->map(fn ($r) => [
            optional($r->created_at)?->format('d/m/Y H:i'),
            $r->vehicle?->registration ?? '-',
            trim(($r->mechanic?->first_name ?? '') . ' ' . ($r->mechanic?->last_name ?? '')),
            $r->type_label,
            $r->priority_label,
            (float) ($r->cost ?? 0),
            (float) ($r->quality_rating ?? 0),
            (float) ($r->delay_rating ?? 0),
        ])->all();

        return Excel::download(
            new TableExport(['Date', 'Vehicule', 'Mecanicien', 'Type', 'Priorite', 'Cout (F)', 'Qualite', 'Delai'], $rows),
            "rapport-reparations-{$startDate}-{$endDate}.xlsx"
        );
    }

    public function repairsPdf(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);
        $category = $request->string('vehicle_category')->toString();

        $repairs = Repair::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($category !== '', fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('category', $category)))
            ->with(['vehicle:id,registration', 'mechanic:id,first_name,last_name'])
            ->orderByDesc('created_at')
            ->get();

        $headers = ['Date', 'Véhicule', 'Mécanicien', 'Type', 'Priorité', 'Coût (F)', 'Qualité', 'Délai'];
        $rows = $repairs->map(fn ($r) => [
            optional($r->created_at)?->format('d/m/Y H:i'),
            $r->vehicle?->registration ?? '-',
            trim(($r->mechanic?->first_name ?? '') . ' ' . ($r->mechanic?->last_name ?? '')),
            $r->type_label,
            $r->priority_label,
            number_format((float) ($r->cost ?? 0), 0, ',', ' '),
            (string) ($r->quality_rating ?? ''),
            (string) ($r->delay_rating ?? ''),
        ])->all();

        $pdf = Pdf::loadView('pdf.table-report', [
            'title' => 'Rapport réparations véhicules',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'vehicle_category' => $category,
            'generated_at' => now(),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-reparations-{$startDate}-{$endDate}.pdf");
    }

    public function missionsExcel(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $missions = Mission::query()
            ->whereBetween('date_start', [$start, $end])
            ->with(['vehicle:id,registration', 'driver:id,first_name,last_name'])
            ->orderByDesc('date_start')
            ->get();

        $rows = $missions->map(fn ($m) => [
            optional($m->date_start)?->format('d/m/Y H:i'),
            optional($m->date_end)?->format('d/m/Y H:i'),
            $m->vehicle?->registration ?? '-',
            trim(($m->driver?->first_name ?? '') . ' ' . ($m->driver?->last_name ?? '')),
            $m->destination ?? '-',
            (int) ($m->distance_km ?? 0),
            $m->status_label,
        ])->all();

        return Excel::download(
            new TableExport(['Date depart', 'Date retour', 'Vehicule', 'Chauffeur', 'Destination', 'Distance (km)', 'Statut'], $rows),
            "rapport-missions-deplacements-{$startDate}-{$endDate}.xlsx"
        );
    }

    public function missionsPdf(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $missions = Mission::query()
            ->whereBetween('date_start', [$start, $end])
            ->with(['vehicle:id,registration', 'driver:id,first_name,last_name'])
            ->orderByDesc('date_start')
            ->get();

        $headers = ['Départ', 'Retour', 'Véhicule', 'Chauffeur', 'Destination', 'Distance (km)', 'Statut'];
        $rows = $missions->map(fn ($m) => [
            optional($m->date_start)?->format('d/m/Y H:i'),
            optional($m->date_end)?->format('d/m/Y H:i'),
            $m->vehicle?->registration ?? '-',
            trim(($m->driver?->first_name ?? '') . ' ' . ($m->driver?->last_name ?? '')),
            $m->destination ?? '-',
            (int) ($m->distance_km ?? 0),
            $m->status_label,
        ])->all();

        $pdf = Pdf::loadView('pdf.table-report', [
            'title' => 'Rapport missions / déplacements',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-missions-deplacements-{$startDate}-{$endDate}.pdf");
    }

    public function sinistresExcel(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $sinistres = Sinistre::query()
            ->whereBetween('declared_at', [$start, $end])
            ->with(['vehicle:id,registration'])
            ->orderByDesc('declared_at')
            ->get();

        $rows = $sinistres->map(fn ($s) => [
            optional($s->declared_at)?->format('d/m/Y H:i'),
            $s->vehicle?->registration ?? '-',
            $s->location ?? '-',
            (float) ($s->estimated_cost ?? 0),
            $s->status_label,
            $s->responsibility ?? '-',
        ])->all();

        return Excel::download(
            new TableExport(['Date declaration', 'Vehicule', 'Lieu', 'Cout estime (F)', 'Statut', 'Responsabilite'], $rows),
            "rapport-sinistres-{$startDate}-{$endDate}.xlsx"
        );
    }

    public function sinistresPdf(Request $request)
    {
        [$start, $end, $startDate, $endDate] = $this->getPeriod($request);

        $sinistres = Sinistre::query()
            ->whereBetween('declared_at', [$start, $end])
            ->with(['vehicle:id,registration'])
            ->orderByDesc('declared_at')
            ->get();

        $headers = ['Déclaration', 'Véhicule', 'Lieu', 'Coût estimé (F)', 'Statut', 'Responsabilité'];
        $rows = $sinistres->map(fn ($s) => [
            optional($s->declared_at)?->format('d/m/Y H:i'),
            $s->vehicle?->registration ?? '-',
            $s->location ?? '-',
            number_format((float) ($s->estimated_cost ?? 0), 0, ',', ' '),
            $s->status_label,
            $s->responsibility ?? '-',
        ])->all();

        $pdf = Pdf::loadView('pdf.table-report', [
            'title' => 'Rapport sinistres',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-sinistres-{$startDate}-{$endDate}.pdf");
    }

    public function missionsAnalyticsExcel(Request $request)
    {
        [$rows, $stats, $startDate, $endDate] = $this->getMissionAnalyticsData($request);

        return Excel::download(
            new TableExport(['Date départ', 'Date retour', 'Chauffeur', 'Direction', 'Destination', 'Distance (km)', 'Statut'], $rows),
            "rapport-analyses-deplacements-{$startDate}-{$endDate}.xlsx"
        );
    }

    public function missionsAnalyticsPdf(Request $request)
    {
        [$rows, $stats, $startDate, $endDate] = $this->getMissionAnalyticsData($request);

        $title = 'Analyses des déplacements';
        $headers = ['Date départ', 'Date retour', 'Chauffeur', 'Direction', 'Destination', 'Distance (km)', 'Statut'];
        $pdf = Pdf::loadView('pdf.table-report', [
            'title' => $title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-analyses-deplacements-{$startDate}-{$endDate}.pdf");
    }
}
