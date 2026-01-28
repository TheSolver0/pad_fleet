<div>
    <p class="section-label">Affectations véhicules</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Affectations</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Véhicule, marque, personne..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 180px;" wire:model.live="filter_type">
                        <option value="">Tous les types</option>
                        @foreach(\App\Models\Vehicle::assignmentTypeOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle affectation
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Véhicule</th>
                        <th>Marque / Modèle</th>
                        <th>Personne assignée</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $v)
                        <tr>
                            <td><span class="fw-medium">{{ $v->registration }}</span></td>
                            <td>{{ $v->vehicleModel?->full_name ?? '—' }}</td>
                            <td>{{ $v->assignedPerson?->name ?? '—' }}</td>
                            <td>{{ $v->assignment_type_label ?? '—' }}</td>
                            <td class="small">{{ $v->assignment_period_label ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('vehicles.index') }}" class="btn btn-sm btn-outline-secondary me-1" title="Voir véhicule"><i class="bi bi-car-front"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" wire:click="openEdit({{ $v->id }})" title="Modifier"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeAssignment({{ $v->id }})" title="Retirer l'affectation"><i class="bi bi-x-lg"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucune affectation. Cliquez sur « Nouvelle affectation » pour assigner un véhicule à une personne, ou affectez depuis la fiche véhicule.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assignments->hasPages())
            <div class="p-3 border-top">{{ $assignments->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.affectations.partials.modal-assignment')
    @include('livewire.portal.affectations.partials.modal-quick-add-person')
</div>
