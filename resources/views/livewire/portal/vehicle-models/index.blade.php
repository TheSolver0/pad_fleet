<div>
    <p class="section-label">Modèles de véhicules — Par marque</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Modèles</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Nom du modèle..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 160px;" wire:model.live="brand_filter">
                        <option value="">Toutes les marques</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau modèle
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Marque</th>
                        <th>Modèle</th>
                        <th>Code</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($models as $m)
                        <tr>
                            <td><span class="fw-medium">{{ $m->brand?->name }}</span></td>
                            <td>{{ $m->name }}</td>
                            <td>{{ $m->code ?? '—' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $m->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $m->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Aucun modèle.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($models->hasPages())
            <div class="p-3 border-top">{{ $models->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.vehicle-models.partials.modal-form')
    @include('livewire.portal.vehicle-models.partials.modal-delete')
</div>
