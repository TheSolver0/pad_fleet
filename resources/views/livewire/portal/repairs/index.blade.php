<div>
    <p class="section-label">Réparations — Interne/Externe, fiche de transfert, suivi coûts</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Réparations</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 180px;" placeholder="Véhicule..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 120px;" wire:model.live="type_filter">
                        <option value="">Toutes</option>
                        <option value="internal">Interne</option>
                        <option value="external">Externe</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle réparation
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Véhicule</th>
                        <th>Garage</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Coût</th>
                        <th>Début / Fin</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairs as $r)
                        <tr>
                            <td>{{ $r->vehicle?->registration ?? '—' }}</td>
                            <td>{{ $r->garage?->name ?? '—' }}</td>
                            <td><span class="badge {{ $r->type === 'internal' ? 'bg-primary' : 'bg-secondary' }}">{{ $r->type_label }}</span></td>
                            <td>{{ Str::limit($r->description, 35) }}</td>
                            <td>{{ $r->cost ? format_money($r->cost, 0) . ' F' : '—' }}</td>
                            <td class="small">{{ $r->started_at?->format('d/m/Y') ?? '—' }} → {{ $r->completed_at?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $r->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $r->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucune réparation.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($repairs->hasPages())
            <div class="p-3 border-top">{{ $repairs->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.repairs.partials.modal-form')
    @include('livewire.portal.repairs.partials.modal-delete')
</div>
