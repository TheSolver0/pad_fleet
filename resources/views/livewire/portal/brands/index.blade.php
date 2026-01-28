<div>
    <p class="section-label">Marques de véhicules</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Marques</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, code..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle marque
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Modèles</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $b)
                        <tr>
                            <td><span class="fw-medium">{{ $b->name }}</span></td>
                            <td>{{ $b->code ?? '—' }}</td>
                            <td>{{ $b->vehicle_models_count }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $b->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $b->id }})" @if($b->vehicle_models_count > 0) disabled title="Supprimer d'abord les modèles" @endif><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Aucune marque.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($brands->hasPages())
            <div class="p-3 border-top">{{ $brands->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.brands.partials.modal-form')
    @include('livewire.portal.brands.partials.modal-delete')
</div>
