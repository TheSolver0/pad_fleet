<div>
    <p class="section-label">Assurances globales — Marchés par lots, alertes échéances, upload contrats</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Marchés assurance</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Nom, assureur..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau marché
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th>Assureur</th>
                        <th>Lot</th>
                        <th>Période</th>
                        <th>Prime optionnelle</th>
                        <th>Alerte</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $c)
                        <tr>
                            <td><span class="fw-medium">{{ $c->name }}</span></td>
                            <td>{{ $c->insurer }}</td>
                            <td class="small">{{ Str::limit($c->lot_description, 30) ?? '—' }}</td>
                            <td class="small">{{ $c->start_date->format('d/m/Y') }} → {{ $c->end_date->format('d/m/Y') }}</td>
                            <td>{{ $c->optional_prime ? format_money($c->optional_prime, 0) . ' F' : '—' }}</td>
                            <td>
                                @if($c->isExpired())
                                    <span class="badge bg-danger">Expiré</span>
                                @elseif($c->isExpiringSoon(30))
                                    <span class="badge bg-warning text-dark">Expire bientôt</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $c->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openDocModal({{ $c->id }})"><i class="bi bi-file-earmark"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $c->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun marché.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
            <div class="p-3 border-top">{{ $contracts->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.assurances.partials.modal-form')
    @include('livewire.portal.assurances.partials.modal-documents')
    @include('livewire.portal.assurances.partials.modal-delete')
</div>
