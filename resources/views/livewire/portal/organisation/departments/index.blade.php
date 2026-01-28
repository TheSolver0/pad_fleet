<div>
    <p class="section-label">Organisation — Départements</p>
    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Départements</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Nom, code..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 180px;" wire:model.live="filter_direction">
                        <option value="">Toutes directions</option>
                        @foreach($directions as $dir)
                            <option value="{{ $dir->id }}">{{ $dir->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate"><i class="bi bi-plus-lg me-1"></i> Nouveau département</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr><th>Direction</th><th>Nom</th><th>Code</th><th>Services</th><th>Personnes</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($departments as $d)
                        <tr>
                            <td>{{ $d->direction?->name ?? '—' }}</td>
                            <td><span class="fw-medium">{{ $d->name }}</span></td>
                            <td>{{ $d->code ?? '—' }}</td>
                            <td>{{ $d->org_services_count }}</td>
                            <td>{{ $d->persons_count }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $d->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $d->id }})" @if($d->org_services_count > 0) disabled title="Supprimer d'abord les services" @endif><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Aucun département.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages()) <div class="p-3 border-top">{{ $departments->links() }}</div> @endif
    </div>
    @include('livewire.portal.organisation.departments.partials.modal-form')
    @include('livewire.portal.organisation.departments.partials.modal-delete')
</div>
