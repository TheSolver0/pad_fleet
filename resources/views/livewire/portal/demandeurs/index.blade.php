<div>
    <p class="section-label">Demandeurs — Bénéficiaires des réservations véhicules</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Demandeurs</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, matricule..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-person-plus me-1"></i> Nouveau demandeur
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Lié à</th>
                        <th>Service</th>
                        <th>Contact</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($demandeurs as $d)
                        <tr>
                            <td>{{ $d->matricule ?? '—' }}</td>
                            <td><span class="fw-medium">{{ $d->name }}</span></td>
                            <td><span class="badge bg-light text-dark">{{ $d->type_label }}</span></td>
                            <td>
                                @if($d->demandeur_type === 'person')
                                    {{ $d->person?->name ?? '—' }}
                                @else
                                    {{ $d->direction?->name ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $d->service?->name ?? '—' }}</td>
                            <td>
                                @if($d->contact_phone || $d->contact_email)
                                    {{ $d->contact_phone ?? '—' }} / {{ $d->contact_email ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $d->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $d->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun demandeur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($demandeurs->hasPages())
            <div class="p-3 border-top">{{ $demandeurs->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.demandeurs.partials.modal-form')
    @include('livewire.portal.demandeurs.partials.modal-delete')
</div>
