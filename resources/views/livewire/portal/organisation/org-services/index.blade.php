<div>
    <p class="section-label">Organisation — Services</p>
    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Services</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Nom, code..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 200px;" wire:model.live="filter_department">
                        <option value="">Tous départements</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->direction?->name ?? '' }} — {{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate"><i class="bi bi-plus-lg me-1"></i> Nouveau service</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr><th>Direction / Département</th><th>Service</th><th>Code</th><th>Personnes</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($orgServices as $s)
                        <tr>
                            <td><span class="text-muted small">{{ $s->department?->direction?->name ?? '—' }} / {{ $s->department?->name ?? '—' }}</span></td>
                            <td><span class="fw-medium">{{ $s->name }}</span></td>
                            <td>{{ $s->code ?? '—' }}</td>
                            <td>{{ $s->persons_count }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $s->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $s->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucun service.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orgServices->hasPages()) <div class="p-3 border-top">{{ $orgServices->links() }}</div> @endif
    </div>
    @include('livewire.portal.organisation.org-services.partials.modal-form')
    @include('livewire.portal.organisation.org-services.partials.modal-delete')
</div>
