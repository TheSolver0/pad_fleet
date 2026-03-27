<div>
    <p class="section-label">Sinistres — Déclaration accidents, coût estimé, garage, upload photos</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Sinistres</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;"
                        placeholder="Description..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 130px;" wire:model.live="status_filter">
                        <option value="">Tous</option>
                        <option value="declared">Déclaré</option>
                        <option value="in_repair">En réparation</option>
                        <option value="closed">Clôturé</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Déclarer un sinistre
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Chauffeur</th>
                        <th>Description</th>
                        <th>Coût estimé</th>
                        <th>Responsabilité</th>
                        <th>Garage</th>
                        <th>Statut</th>

                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sinistres as $s)
                        <tr>
                            <td class="small">{{ $s->declared_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $s->vehicle?->registration ?? '—' }}</td>
                            <td>
                                @if ($s->driver)
                                    <span class="fw-medium">{{ $s->driver->full_name }}</span>
                                    @if ($s->driver->matricule)
                                        <br><small class="text-muted">{{ $s->driver->matricule }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($s->description, 40) }}</td>
                            <td>{{ $s->estimated_cost ? format_money($s->estimated_cost, 0) . ' F' : '—' }}</td>
                            <td>{{ $s->responsibility ?? '—' }}</td>
                            <td>{{ $s->garage?->name ?? '—' }}</td>
                            <td><span
                                    class="badge {{ $s->status === 'closed' ? 'bg-success' : ($s->status === 'in_repair' ? 'bg-warning text-dark' : 'bg-info') }}">{{ $s->status_label }}</span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    wire:click="openEdit({{ $s->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    wire:click="openPhotoModal({{ $s->id }})"><i
                                        class="bi bi-image"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    wire:click="confirmDelete({{ $s->id }})"><i
                                        class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Aucun sinistre.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($sinistres->hasPages())
            <div class="p-3 border-top">{{ $sinistres->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.sinistres.partials.modal-form')
    @include('livewire.portal.sinistres.partials.modal-photos')
    @include('livewire.portal.sinistres.partials.modal-delete')
</div>
