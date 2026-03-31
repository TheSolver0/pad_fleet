<div>
    <p class="section-label">Mes travaux — Espace mécanicien</p>

    @if(!$mechanic)
        <div class="alert alert-warning">Votre compte n'est pas encore lié à une fiche mécanicien. Contactez l'administrateur.</div>
    @else
        <div class="alert alert-light border">
            <strong>{{ $mechanic->full_name }}</strong>
            @if($mechanic->specialization) — {{ $mechanic->specialization }} @endif
        </div>

        <div class="activity-card mb-4">
            <div class="activity-card-header">
                <div class="module-toolbar">
                    <span class="module-toolbar-title">Travaux assignés</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Véhicule</th>
                            <th>Garage</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Début</th>
                            <th>Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repairs as $r)
                            <tr>
                                <td>#{{ $r->id }}</td>
                                <td>{{ $r->vehicle?->registration ?? '—' }}</td>
                                <td>{{ $r->garage?->name ?? '—' }}</td>
                                <td>{{ $r->type_label }}</td>
                                <td>{{ \Str::limit($r->description, 50) }}</td>
                                <td>{{ $r->started_at?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $r->completed_at?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Aucun travail assigné.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($repairs, 'links'))
                <div class="card-footer bg-light">{{ $repairs->links() }}</div>
            @endif
        </div>
    @endif
</div>
