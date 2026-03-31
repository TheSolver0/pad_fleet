<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Database\Seeders\Fictive\FictiveReportsSeeder;
use App\Exports\TableExport;
use App\Exports\VehicleConsumptionExport;
use App\Models\Driver;
use App\Models\Mission;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\Sinistre;
use App\Models\StockMovement;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('alerts:vehicle-holders {--days=30}', function () {
    $days = (int) $this->option('days');
    $limitDate = now()->addDays($days)->endOfDay();

    $vehicles = \App\Models\Vehicle::with(['assignedPerson', 'documents', 'carteGrises'])
        ->whereNotNull('assigned_person_id')
        ->get();

    $sent = 0;
    foreach ($vehicles as $vehicle) {
        $email = $vehicle->assignedPerson?->email;
        if (! $email) {
            continue;
        }

        $expiringDocs = $vehicle->documents
            ->filter(fn ($d) => $d->expires_at && $d->expires_at->lte($limitDate));
        $expiringCarteGrises = $vehicle->carteGrises
            ->filter(fn ($cg) => $cg->expires_at && $cg->expires_at->lte($limitDate));

        if ($expiringDocs->isEmpty() && $expiringCarteGrises->isEmpty()) {
            continue;
        }

        $lines = ["Alerte documents vehicule {$vehicle->registration}:"];
        foreach ($expiringDocs as $doc) {
            $lines[] = "- {$doc->type_label} expire le {$doc->expires_at->format('d/m/Y')}";
        }
        foreach ($expiringCarteGrises as $cg) {
            $lines[] = "- Carte grise {$cg->reference_number} expire le {$cg->expires_at->format('d/m/Y')}";
        }

        Mail::raw(implode("\n", $lines), function ($m) use ($email, $vehicle) {
            $m->to($email)->subject("Alerte documents - Vehicule {$vehicle->registration}");
        });
        $sent++;
    }

    $this->info("Alertes envoyees: {$sent}");
})->purpose('Send expiring document alerts to vehicle holders');

Schedule::command('alerts:vehicle-holders --days=30')->dailyAt('07:30');

Artisan::command('reports:send-automatic
    {--period=weekly : weekly|monthly}
    {--emails= : Comma-separated recipients (fallback: REPORT_AUTO_RECIPIENTS)}
    {--category= : Vehicle category filter (optional)}
    {--start-date= : Custom start date (Y-m-d)}
    {--end-date= : Custom end date (Y-m-d)}
', function () {
    $period = strtolower((string) $this->option('period'));
    if (! in_array($period, ['weekly', 'monthly'], true)) {
        $this->error("Invalid --period '{$period}'. Allowed: weekly, monthly.");
        return 1;
    }

    $emailsOpt = trim((string) $this->option('emails'));
    $emailsRaw = $emailsOpt !== '' ? $emailsOpt : (string) env('REPORT_AUTO_RECIPIENTS', '');
    $recipients = collect(explode(',', $emailsRaw))
        ->map(fn ($email) => trim($email))
        ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
        ->values();

    if ($recipients->isEmpty()) {
        $this->warn('No valid recipients found. Set --emails or REPORT_AUTO_RECIPIENTS.');
        return 0;
    }

    $startDateOpt = trim((string) $this->option('start-date'));
    $endDateOpt = trim((string) $this->option('end-date'));
    $now = now();
    [$defaultStartDate, $defaultEndDate] = $period === 'monthly'
        ? [$now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString()]
        : [$now->copy()->subWeek()->startOfWeek()->toDateString(), $now->copy()->subWeek()->endOfWeek()->toDateString()];

    $startDate = $startDateOpt !== '' ? $startDateOpt : $defaultStartDate;
    $endDate = $endDateOpt !== '' ? $endDateOpt : $defaultEndDate;

    try {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
    } catch (\Throwable $e) {
        $this->error('Invalid date format. Use Y-m-d for --start-date and --end-date.');
        return 1;
    }

    if ($start->gt($end)) {
        $this->error('Invalid period: start date must be before or equal to end date.');
        return 1;
    }

    $category = trim((string) $this->option('category'));
    if ($category === '') {
        $category = trim((string) env('REPORT_AUTO_CATEGORY', ''));
    }

    $parts = RepairPart::query()
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

    $rows = $parts->map(fn ($r) => [
        $r['category'],
        $r['name'],
        $r['reference'],
        $r['total_quantity'],
        $r['total_cost'],
        $r['usage_count'],
    ])->all();

    $excelBinary = Excel::raw(new VehicleConsumptionExport($rows), \Maatwebsite\Excel\Excel::XLSX);
    $pdfBinary = Pdf::loadView('pdf.vehicle-consumption', [
        'parts' => $parts,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'vehicle_category' => $category,
        'generated_at' => now(),
    ])->setPaper('a4', 'portrait')->output();

    $missions = Mission::query()
        ->whereBetween('date_start', [$start, $end])
        ->whereIn('status', [Mission::STATUS_APPROVED, Mission::STATUS_COMPLETED])
        ->with(['vehicle:id,registration', 'driver:id,first_name,last_name'])
        ->orderByDesc('date_start')
        ->get();

    $repairs = Repair::query()
        ->whereBetween('created_at', [$start, $end])
        ->with(['vehicle:id,registration', 'mechanic:id,first_name,last_name'])
        ->orderByDesc('created_at')
        ->get();

    $sinistres = Sinistre::query()
        ->whereBetween('declared_at', [$start, $end])
        ->with(['vehicle:id,registration'])
        ->orderByDesc('declared_at')
        ->get();

    $missionsRows = $missions->map(fn ($m) => [
        optional($m->date_start)?->format('d/m/Y H:i'),
        optional($m->date_end)?->format('d/m/Y H:i'),
        $m->vehicle?->registration ?? '-',
        trim(($m->driver?->first_name ?? '') . ' ' . ($m->driver?->last_name ?? '')),
        $m->destination ?? '-',
        (int) ($m->distance_km ?? 0),
        $m->status_label,
    ])->all();

    $repairsRows = $repairs->map(fn ($r) => [
        optional($r->created_at)?->format('d/m/Y H:i'),
        $r->vehicle?->registration ?? '-',
        trim(($r->mechanic?->first_name ?? '') . ' ' . ($r->mechanic?->last_name ?? '')),
        $r->type_label,
        $r->priority_label,
        (float) ($r->cost ?? 0),
        (float) ($r->quality_rating ?? 0),
        (float) ($r->delay_rating ?? 0),
    ])->all();

    $sinistresRows = $sinistres->map(fn ($s) => [
        optional($s->declared_at)?->format('d/m/Y H:i'),
        $s->vehicle?->registration ?? '-',
        $s->location ?? '-',
        (float) ($s->estimated_cost ?? 0),
        $s->status_label,
        $s->responsibility ?? '-',
    ])->all();

    $missionsExcel = Excel::raw(new TableExport(
        ['Date depart', 'Date retour', 'Vehicule', 'Chauffeur', 'Destination', 'Distance (km)', 'Statut'],
        $missionsRows
    ), \Maatwebsite\Excel\Excel::XLSX);

    $repairsExcel = Excel::raw(new TableExport(
        ['Date', 'Vehicule', 'Mecanicien', 'Type', 'Priorite', 'Cout (F)', 'Qualite', 'Delai'],
        $repairsRows
    ), \Maatwebsite\Excel\Excel::XLSX);

    $sinistresExcel = Excel::raw(new TableExport(
        ['Date declaration', 'Vehicule', 'Lieu', 'Cout estime (F)', 'Statut', 'Responsabilite'],
        $sinistresRows
    ), \Maatwebsite\Excel\Excel::XLSX);

    $stockMovements = StockMovement::query()
        ->whereBetween('created_at', [$start, $end])
        ->with('article:id,name,reference')
        ->orderByDesc('created_at')
        ->get();

    $stockEntries = $stockMovements->where('type', StockMovement::TYPE_ENTRY);
    $stockExits = $stockMovements->where('type', StockMovement::TYPE_EXIT);

    $stockRows = $stockMovements->map(fn ($sm) => [
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

    $stockExcel = Excel::raw(new TableExport(
        ['Date', 'Type', 'Magasin', 'Article', 'Ref', 'Qte', 'PU (F)', 'Total (F)', 'Reference', 'Motif'],
        $stockRows
    ), \Maatwebsite\Excel\Excel::XLSX);

    // Consommation par vehicule (pieces) depuis les interventions (repair_parts -> repairs -> vehicles).
    $vehicleParts = \Illuminate\Support\Facades\DB::table('repair_parts')
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
        ->limit(2000)
        ->get();

    $vehicleStockRows = $vehicleParts->map(fn ($r) => [
        $r->registration,
        $r->category,
        $r->article_name,
        $r->article_reference,
        (int) $r->total_quantity,
        (float) $r->total_cost,
    ])->all();

    $vehicleStockExcel = Excel::raw(new TableExport(
        ['Vehicule', 'Categorie', 'Piece', 'Reference', 'Quantite consommee', 'Cout total (F)'],
        $vehicleStockRows
    ), \Maatwebsite\Excel\Excel::XLSX);

    // Rapport chauffeurs (missions + km + sinistres).
    $driverStats = Driver::query()
        ->withCount([
            'missions as missions_completed_count' => fn ($q) => $q->whereBetween('date_start', [$start, $end])->where('status', \App\Models\Mission::STATUS_COMPLETED),
        ])
        ->withSum([
            'missions as missions_distance_km_sum' => fn ($q) => $q->whereBetween('date_start', [$start, $end])->where('status', \App\Models\Mission::STATUS_COMPLETED),
        ], 'distance_km')
        ->get();

    $driverRows = $driverStats->map(function ($d) use ($start, $end) {
        $sinistresCount = \App\Models\Sinistre::query()
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

    $driversExcel = Excel::raw(new TableExport(
        ['Matricule', 'Chauffeur', 'Telephone', 'Email', 'Missions terminees', 'Distance (km)', 'Sinistres', 'Disponibilite'],
        $driverRows
    ), \Maatwebsite\Excel\Excel::XLSX);

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

    $executivePdf = Pdf::loadView('pdf.fleet-executive-summary', [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'vehicle_category' => $category,
        'generated_at' => now(),
        'stats' => $stats,
        'top_parts' => $parts->take(15),
    ])->setPaper('a4', 'portrait')->output();

    $label = $period === 'monthly' ? 'mensuel' : 'hebdomadaire';
    if ($startDateOpt !== '' || $endDateOpt !== '') {
        $label .= ' (personnalise)';
    }
    $subject = "Rapport flotte {$label} - PAD ({$startDate} au {$endDate})";
    $bodyLines = [
        'Bonjour,',
        '',
        "Veuillez trouver en pieces jointes le pack de rapports {$label}.",
        "Periode: {$startDate} au {$endDate}",
        'Categorie: ' . ($category !== '' ? $category : 'Toutes'),
        'Missions: ' . $stats['missions_count'] . ' | Distance: ' . number_format($stats['missions_distance'], 0, ',', ' ') . ' km',
        'Reparations: ' . $stats['repairs_count'] . ' | Cout: ' . number_format($stats['repairs_cost'], 0, ',', ' ') . ' F',
        'Sinistres: ' . $stats['sinistres_count'] . ' | Cout estime: ' . number_format($stats['sinistres_estimated_cost'], 0, ',', ' ') . ' F',
        'Consommation pieces: ' . number_format($stats['parts_quantity'], 0, ',', ' ') . ' unites | ' . number_format($stats['parts_cost'], 0, ',', ' ') . ' F',
        'Stock - Entrees: ' . number_format($stats['stock_entries_qty'], 0, ',', ' ') . ' | ' . number_format($stats['stock_entries_cost'], 0, ',', ' ') . ' F',
        'Stock - Sorties: ' . number_format($stats['stock_exits_qty'], 0, ',', ' ') . ' | ' . number_format($stats['stock_exits_cost'], 0, ',', ' ') . ' F',
        '',
        'Cordialement,',
        'Direction des Affaires Generales - PAD Fleet',
    ];

    foreach ($recipients as $email) {
        Mail::raw(implode("\n", $bodyLines), function ($m) use ($email, $subject, $period, $startDate, $endDate, $excelBinary, $pdfBinary, $executivePdf, $missionsExcel, $repairsExcel, $sinistresExcel, $stockExcel, $vehicleStockExcel, $driversExcel) {
            $m->to($email)
                ->subject($subject)
                ->attachData($executivePdf, "synthese-executive-{$period}-{$startDate}-{$endDate}.pdf", [
                    'mime' => 'application/pdf',
                ])
                ->attachData($excelBinary, "rapport-consommation-{$period}-{$startDate}-{$endDate}.xlsx", [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->attachData($pdfBinary, "rapport-consommation-{$period}-{$startDate}-{$endDate}.pdf", [
                    'mime' => 'application/pdf',
                ])
                ->attachData($vehicleStockExcel, "rapport-stock-par-vehicule-{$period}-{$startDate}-{$endDate}.xlsx", [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->attachData($stockExcel, "rapport-gestion-stock-entrees-sorties-{$period}-{$startDate}-{$endDate}.xlsx", [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->attachData($driversExcel, "rapport-chauffeurs-{$period}-{$startDate}-{$endDate}.xlsx", [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->attachData($missionsExcel, "rapport-missions-deplacements-{$period}-{$startDate}-{$endDate}.xlsx", [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->attachData($repairsExcel, "rapport-reparations-{$period}-{$startDate}-{$endDate}.xlsx", [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->attachData($sinistresExcel, "rapport-sinistres-{$period}-{$startDate}-{$endDate}.xlsx", [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
        });
    }

    $this->info('Rapports automatiques envoyes: ' . $recipients->count() . ' destinataire(s).');
    return 0;
})->purpose('Send weekly/monthly automatic vehicle consumption reports');

Schedule::command('reports:send-automatic --period=weekly')->weeklyOn(5, '17:00');
Schedule::command('reports:send-automatic --period=monthly')->monthlyOn(27, '17:00');

Artisan::command('fictive:seed-reports {--years=5 : Number of years to generate} {--reset : Purge previous fictive dataset before seeding}', function () {
    $years = (int) $this->option('years');
    $reset = (bool) $this->option('reset');

    $result = (new FictiveReportsSeeder())
        ->configure($years, $reset)
        ->run();

    $this->info('Seed fictif termine.');
    $this->line(" - Annees: {$result['years']}");
    $this->line(" - Vehicules fictifs: {$result['vehicles']}");
    $this->line(" - Reparations creees: {$result['repairs']}");
    $this->line(" - Lignes de pieces creees: {$result['parts']}");
})->purpose('Seed coherent fictive report data (isolated from real seeding)');
