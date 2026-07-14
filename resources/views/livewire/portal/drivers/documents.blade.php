<div>
    <p class="section-label">Documents chauffeur — Assurances et cartes grises</p>

    @if(!$driver)
        <div class="alert alert-warning">Aucun profil chauffeur n'est lié à votre compte.</div>
    @endif

    @forelse($vehicles as $vehicle)
        <div class="activity-card mb-3" wire:key="driver-doc-vehicle-{{ $vehicle->id }}">
            <div class="activity-card-header">
                <div class="module-toolbar">
                    <span class="module-toolbar-title">Véhicule {{ $vehicle->registration }}</span>
                </div>
            </div>
            <div class="p-3">
                <h6>Documents d'assurance / carte grise</h6>
                <ul class="list-group list-group-flush mb-3">
                    @forelse($vehicle->documents as $doc)
                        <li wire:key="driver-doc-{{ $doc->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-light text-dark me-2">{{ $doc->type_label }}</span>
                                {{ $doc->original_name ?: basename($doc->file_path) }}
                                @if($doc->expires_at)
                                    <small class="text-muted">— Expire le {{ $doc->expires_at->format('d/m/Y') }}</small>
                                @endif
                            </span>
                            <a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                                <i class="bi bi-download"></i>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Aucun document.</li>
                    @endforelse
                </ul>

                <h6>Historique cartes grises</h6>
                <ul class="list-group list-group-flush">
                    @forelse($vehicle->carteGrises as $cg)
                        <li wire:key="driver-cg-{{ $cg->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                {{ $cg->reference_number ?? '—' }}
                                <small class="text-muted"> — Délivrance {{ $cg->issued_at?->format('d/m/Y') ?? '—' }}, expiration {{ $cg->expires_at?->format('d/m/Y') ?? '—' }}</small>
                            </span>
                            @if($cg->file_path)
                                <a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/' . $cg->file_path) }}" target="_blank">
                                    <i class="bi bi-download"></i>
                                </a>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Aucune carte grise.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Aucun véhicule lié à vos missions pour le moment.</div>
    @endforelse
</div>
