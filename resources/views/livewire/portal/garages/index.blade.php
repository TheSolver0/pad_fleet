<div>
    <p class="section-label">Garages — Référentiel internes et partenaires externes</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Garages</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Nom..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 130px;" wire:model.live="type_filter">
                        <option value="">Tous</option>
                        <option value="internal">Interne</option>
                        <option value="external">Externe</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau garage
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Coordonnées</th>
                        <th>Actif</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($garages as $g)
                        <tr>
                            <td><span class="fw-medium">{{ $g->name }}</span></td>
                            <td><span class="badge {{ $g->type === 'internal' ? 'bg-primary' : 'bg-secondary' }}">{{ $g->type_label }}</span></td>
                            <td class="small">{{ $g->phone ?? '—' }} @if($g->email) / {{ $g->email }} @endif</td>
                            <td>@if($g->is_active) <span class="badge bg-success">Oui</span> @else <span class="badge bg-secondary">Non</span> @endif</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $g->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $g->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Aucun garage.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($garages->hasPages())
            <div class="p-3 border-top">{{ $garages->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.garages.partials.modal-form')
    @include('livewire.portal.garages.partials.modal-delete')
</div>
