<div>
    <p class="section-label">Planning missions — Réservations véhicules, KM départ/retour, workflow approbation</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Missions</span>
                <div class="module-toolbar-filters">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn {{ $view_mode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('view_mode', 'list')">
                            <i class="bi bi-list-ul me-1"></i> Liste
                        </button>
                        <button type="button" class="btn {{ $view_mode === 'calendar' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('view_mode', 'calendar')">
                            <i class="bi bi-calendar3 me-1"></i> Calendrier
                        </button>
                    </div>
                    @if($view_mode === 'list')
                        <input type="text" class="form-control form-control-sm" style="width: 180px;" placeholder="Véhicule, demandeur..." wire:model.live.debounce.300ms="search">
                        <select class="form-select form-select-sm" style="width: 130px;" wire:model.live="status_filter">
                            <option value="">Tous statuts</option>
                            <option value="pending">En attente</option>
                            <option value="programmed">Programmée</option>
                            <option value="in_progress">En cours</option>
                            <option value="postponed">Reportée</option>
                            <option value="rejected">Refusée</option>
                            <option value="completed">Terminée</option>
                        </select>
                        <input type="date" class="form-control form-control-sm" style="width: 145px;" wire:model.live="filter_start" title="Date de début">
                        <span class="text-muted small align-self-center">→</span>
                        <input type="date" class="form-control form-control-sm" style="width: 145px;" wire:model.live="filter_end" title="Date de fin">
                        @if($filter_start !== '' || $filter_end !== '')
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetPeriodFilter" title="Effacer la période">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        @endif
                    @else
                        <input type="month" class="form-control form-control-sm" style="width: 160px;" wire:model.live="month_calendar">
                    @endif
                </div>
                <div class="module-toolbar-actions">
                    <a href="{{ route('missions.analytics') }}" class="btn btn-sm btn-outline-primary me-2">
                        <i class="bi bi-graph-up me-1"></i> Synthèse
                    </a>
                    <button type="button" class="btn btn-sm btn-info me-2" wire:click="openReportModal">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Rapport
                    </button>
                    @can('planning-missions')
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle réservation
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        @if($view_mode === 'list')
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Véhicule</th>
                            <th>Chauffeur</th>
                            <th>Demandeur</th>
                            <th>Période</th>
                            <th>Destination</th>
                            <th>Techniciens</th>
                            <th>KM / Distance</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($missions as $m)
                            <tr>
                                <td>{{ $m->vehicle?->registration ?? '—' }}</td>
                                <td>{{ $m->driver ? $m->driver->full_name : '—' }}</td>
                                <td>{{ $m->demandeur?->name ?? '—' }}</td>
                                <td class="small">{{ $m->date_start->format('d/m/Y') }} → {{ $m->date_end->format('d/m/Y') }}</td>
                                <td>{{ Str::limit($m->destination, 20) ?? '—' }}</td>
                                <td>
                                    @if($m->technicians->isNotEmpty())
                                        <span class="badge bg-secondary">{{ $m->technicians->count() }} affecté(s)</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $m->km_departure ?? '—' }} / {{ $m->km_return ?? '—' }} @if($m->distance_km) ({{ $m->distance_km }} km) @endif</td>
                                <td>
                                    @php
                                        $badge = match($m->status) {
                                            'pending' => 'bg-warning text-dark',
                                            'approved', 'programmed' => 'bg-primary',
                                            'in_progress' => 'bg-info',
                                            'postponed' => 'bg-dark',
                                            'rejected' => 'bg-danger',
                                            'completed' => 'bg-success',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $m->status_label }}</span>
                                </td>
                                <td class="text-end">
                                    @can('planning-missions')
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $m->id }})"><i class="bi bi-pencil"></i></button>
                                    @if($m->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-outline-success" wire:click="openApproveModal({{ $m->id }})"><i class="bi bi-check-lg"></i></button>
                                    @endif
                                    @endcan
                                    @can('comptes-rendus')
                                    <button type="button" class="btn btn-sm btn-outline-info" wire:click="openCompteRenduModal({{ $m->id }})" title="Compte-rendu de mission">
                                        <i class="bi bi-journal-text"></i>
                                        @if($m->compte_rendu)<span class="badge bg-success ms-1">✓</span>@endif
                                    </button>
                                    @endcan
                                    @if(in_array($m->status, ['approved', 'programmed', 'postponed']))
                                        <button type="button" class="btn btn-sm btn-outline-info" wire:click="markInProgress({{ $m->id }})">Démarrer</button>
                                    @endif
                                    @if($m->status === 'in_progress')
                                        <button type="button" class="btn btn-sm btn-outline-success" wire:click="markCompleted({{ $m->id }})">Terminer</button>
                                    @endif
                                    @if(in_array($m->status, ['programmed', 'in_progress', 'approved']))
                                        <button type="button" class="btn btn-sm btn-outline-dark" wire:click="markPostponed({{ $m->id }})">Reporter</button>
                                    @endif
                                    @if($m->status === 'postponed')
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="markProgrammed({{ $m->id }})">Reprogrammer</button>
                                    @endif
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openPhotoModal({{ $m->id }}, 'before')" title="Photos avant mission">
                                            <i class="bi bi-camera"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openPhotoModal({{ $m->id }}, 'after')" title="Photos après mission">
                                            <i class="bi bi-camera-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" wire:click="openDocumentModal({{ $m->id }})" title="Documents">
                                            <i class="bi bi-file-earmark"></i>
                                        </button>
                                        <a href="{{ route('vehicles.control-sheets', ['mission_id' => $m->id]) }}" class="btn btn-sm btn-outline-warning" title="Fiche de contrôle véhicule">
                                            <i class="bi bi-clipboard-check"></i>
                                            @if($m->control_sheets_count > 0)
                                                <span class="badge bg-success ms-1">{{ $m->control_sheets_count }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    @can('planning-missions')
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $m->id }})"><i class="bi bi-trash"></i></button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Aucune mission.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($missions->hasPages())
                <div class="p-3 border-top">{{ $missions->links() }}</div>
            @endif
        @else
            <div class="p-3">
                <div class="row">
                    <div class="col-md-9">
                        <div class="list-group">
                            @foreach($this->calendarMissions as $m)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $m->vehicle?->registration }}</strong> — {{ $m->demandeur?->name }}
                                        <br><small class="text-muted">{{ $m->date_start->format('d/m') }} → {{ $m->date_end->format('d/m') }} · {{ $m->destination ?? '—' }}</small>
                                    </div>
                                    <span class="badge {{
                                        in_array($m->status, ['approved', 'programmed']) ? 'bg-primary' :
                                        ($m->status === 'in_progress' ? 'bg-info' :
                                        ($m->status === 'postponed' ? 'bg-dark' :
                                        ($m->status === 'pending' ? 'bg-warning text-dark' :
                                        ($m->status === 'completed' ? 'bg-success' : 'bg-secondary'))))
                                    }}">{{ $m->status_label }}</span>
                                </div>
                            @endforeach
                            @if($this->calendarMissions->isEmpty())
                                <p class="text-muted mb-0">Aucune mission sur cette période.</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border">
                            <div class="card-header small fw-600">Mois</div>
                            <div class="card-body small">
                                @php
                                    $d = \Carbon\Carbon::parse($month_calendar . '-01');
                                @endphp
                                {{ $d->translatedFormat('F Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @include('livewire.portal.missions.partials.modal-form')
    @include('livewire.portal.missions.partials.modal-approve')
    @include('livewire.portal.missions.partials.modal-delete')
    @include('livewire.portal.missions.partials.modal-photos')
    @include('livewire.portal.missions.partials.modal-documents')
    @include('livewire.portal.missions.partials.modal-report')
    @include('livewire.portal.missions.partials.modal-compte-rendu')
</div>
