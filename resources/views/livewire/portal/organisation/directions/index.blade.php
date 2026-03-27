<div>
    <p class="section-label">Organisation — Directions</p>
    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Directions</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, code..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate"><i class="bi bi-plus-lg me-1"></i> Nouvelle direction</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr><th>Nom</th><th>Code</th><th>Départements</th><th>Personnes</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($directions as $d)
                        <tr>
                            <td><span class="fw-medium">{{ $d->name }}</span></td>
                            <td>{{ $d->code ?? '—' }}</td>
                            <td>{{ $d->departments_count }}</td>
                            <td>{{ $d->persons_count }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $d->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $d->id }})" @if($d->departments_count > 0) disabled title="Supprimer d'abord les départements" @endif><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucune direction.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($directions->hasPages())
            <div class="p-3 border-top">{{ $directions->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
    @include('livewire.portal.organisation.directions.partials.modal-form')
    @include('livewire.portal.organisation.directions.partials.modal-delete')
</div>
