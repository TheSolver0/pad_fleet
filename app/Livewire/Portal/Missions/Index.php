<?php

namespace App\Livewire\Portal\Missions;

use App\Models\Demandeur;
use App\Models\Driver;
use App\Models\Mission;
use App\Models\MissionDocument;
use App\Models\MissionPhoto;
use App\Models\Vehicle;
use App\Exports\MissionReportExport;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $view_mode = 'list'; // list | calendar
    public string $search = '';
    public string $status_filter = '';
    public string $month_calendar = '';

    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public bool $showApproveModal = false;
    public ?int $editingId = null;

    public ?int $vehicle_id = null;
    public ?int $driver_id = null;
    public ?int $demandeur_id = null;
    public string $date_start = '';
    public string $date_end = '';
    public string $km_departure = '';
    public string $km_return = '';
    public string $destination = '';
    public string $notes = '';
    public bool $apply_approve = false;
    public bool $approve_reject = true; // true = approve, false = reject
    
    // Champs pour créer un nouveau demandeur
    public bool $create_demandeur = false;
    public string $new_demandeur_name = '';
    public string $new_demandeur_phone = '';
    public string $new_demandeur_email = '';
    public string $new_demandeur_service = '';
    public ?int $city_id = null;

    // Photos
    public $before_photos = [];
    public $after_photos = [];
    public bool $showPhotoModal = false;
    public ?int $photoMissionId = null;
    public string $photo_type = 'before'; // before or after

    // New city creation
    public bool $create_city = false;
    public string $new_city_name = '';
    public string $new_city_code = '';
    public string $new_city_region = '';

    // Documents
    public $documents = [];
    public bool $showDocumentModal = false;
    public ?int $documentMissionId = null;
    public string $document_type = 'autre';

    // Rapport
    public bool $showReportModal = false;
    public string $report_start_date = '';
    public string $report_end_date = '';
    public string $report_status = '';
    public ?int $report_demandeur_id = null;

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => ''], 'view_mode' => ['except' => 'list']];

    public function mount(): void
    {
        if ($this->month_calendar === '') {
            $this->month_calendar = now()->format('Y-m');
        }
    }

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'demandeur_id' => 'required_without:create_demandeur|exists:demandeurs,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'km_departure' => 'nullable|integer|min:0',
            'km_return' => 'nullable|integer|min:0',
            'destination' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'new_demandeur_name' => 'required_if:create_demandeur,true|string|max:200',
            'new_demandeur_phone' => 'nullable|string|max:30',
            'new_demandeur_email' => 'nullable|email|max:150',
            'new_demandeur_service' => 'nullable|string|max:200',
            'city_id' => 'nullable|exists:cities,id',
            'new_city_name' => 'required_if:create_city,true|string|max:100',
            'new_city_region' => 'required_if:create_city,true|string|max:100',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $m = Mission::findOrFail($id);
        $this->editingId = $m->id;
        $this->vehicle_id = $m->vehicle_id;
        $this->driver_id = $m->driver_id;
        $this->demandeur_id = $m->demandeur_id;
        $this->date_start = $m->date_start->format('Y-m-d');
        $this->date_end = $m->date_end->format('Y-m-d');
        $this->km_departure = $m->km_departure !== null ? (string) $m->km_departure : '';
        $this->km_return = $m->km_return !== null ? (string) $m->km_return : '';
        $this->destination = $m->destination ?? '';
        $this->notes = $m->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveMission(): void
    {
        // Créer le demandeur AVANT validation si nécessaire
        if ($this->create_demandeur && $this->new_demandeur_name) {
            $demandeur = Demandeur::create([
                'name' => $this->new_demandeur_name,
                'contact_phone' => $this->new_demandeur_phone ?: null,
                'contact_email' => $this->new_demandeur_email ?: null,
                'demandeur_type' => 'person', // default type
            ]);
            $this->demandeur_id = $demandeur->id;
        }
        
        // Créer la ville AVANT validation si nécessaire
        if ($this->create_city && $this->new_city_name) {
            $city = \App\Models\City::create([
                'name' => $this->new_city_name,
                'code' => $this->new_city_code ?: null,
                'region' => $this->new_city_region,
                'is_active' => true,
            ]);
            $this->city_id = $city->id;
        }
        
        // Maintenant valider après création
        $this->validate();

        $data = [
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'demandeur_id' => $this->demandeur_id,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'km_departure' => $this->km_departure ? (int) $this->km_departure : null,
            'km_return' => $this->km_return ? (int) $this->km_return : null,
            'destination' => $this->destination ?: null,
            'city_id' => $this->city_id ?: null,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            $mission = Mission::findOrFail($this->editingId);
            $mission->update($data);
            $mission->computeDistance();
            $this->dispatch('notify', type: 'success', message: 'Mission mise à jour.');
        } else {
            $mission = Mission::create(array_merge($data, ['status' => Mission::STATUS_PENDING]));
            $mission->computeDistance();
            $this->dispatch('notify', type: 'success', message: 'Réservation créée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function openApproveModal(int $id): void
    {
        $this->editingId = $id;
        $this->approve_reject = true;
        $this->showApproveModal = true;
    }

    public function approveOrReject(): void
    {
        if (!$this->editingId) {
            return;
        }
        $m = Mission::findOrFail($this->editingId);
        $m->update([
            'status' => $this->approve_reject ? Mission::STATUS_APPROVED : Mission::STATUS_REJECTED,
            'approved_by' => Auth::user()->id,
            'approved_at' => now(),
        ]);
        $this->dispatch('notify', type: 'success', message: $this->approve_reject ? 'Mission approuvée.' : 'Mission refusée.');
        $this->showApproveModal = false;
        $this->editingId = null;
    }

    public function markCompleted(int $id): void
    {
        $m = Mission::findOrFail($id);
        $m->update(['status' => Mission::STATUS_COMPLETED]);
        $m->computeDistance();
        if ($m->vehicle) {
            $m->vehicle->update(['mileage' => $m->km_return ?? $m->vehicle->mileage]);
        }
        $this->dispatch('notify', type: 'success', message: 'Mission marquée terminée.');
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteMission(): void
    {
        if ($this->editingId) {
            Mission::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Mission supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->vehicle_id = null;
        $this->driver_id = null;
        $this->demandeur_id = null;
        $this->date_start = '';
        $this->date_end = '';
        $this->km_departure = '';
        $this->km_return = '';
        $this->destination = '';
        $this->notes = '';
        $this->create_demandeur = false;
        $this->new_demandeur_name = '';
        $this->new_demandeur_phone = '';
        $this->new_demandeur_email = '';
        $this->new_demandeur_service = '';
        $this->city_id = null;
        $this->before_photos = [];
        $this->after_photos = [];
        $this->create_city = false;
        $this->new_city_name = '';
        $this->new_city_code = '';
        $this->new_city_region = '';
        $this->documents = [];
        $this->resetValidation();
    }

    public function updatedCreateCity(): void
    {
        if (!$this->create_city) {
            $this->reset(['new_city_name', 'new_city_region']);
            $this->city_id = null;
        } else {
            $this->city_id = null; // Désélectionner la ville existante
        }
    }

    public function openPhotoModal(int $missionId, string $type = 'before'): void
    {
        $this->photoMissionId = $missionId;
        $this->photo_type = $type;
        $this->before_photos = [];
        $this->after_photos = [];
        $this->showPhotoModal = true;
    }

    public function savePhotos(): void
    {
        $this->validate([
            'before_photos.*' => 'image|max:5120', // 5MB max
            'after_photos.*' => 'image|max:5120',
        ]);

        $mission = Mission::findOrFail($this->photoMissionId);

        // Save before photos
        if ($this->before_photos) {
            foreach ($this->before_photos as $photo) {
                MissionPhoto::storeUpload($mission, $photo, 'before');
            }
        }

        // Save after photos
        if ($this->after_photos) {
            foreach ($this->after_photos as $photo) {
                MissionPhoto::storeUpload($mission, $photo, 'after');
            }
        }

        $this->dispatch('notify', type: 'success', message: 'Photos enregistrées.');
        $this->showPhotoModal = false;
        $this->before_photos = [];
        $this->after_photos = [];
    }

    public function deletePhoto(int $photoId): void
    {
        $photo = MissionPhoto::findOrFail($photoId);
        $photo->delete();
        $this->dispatch('notify', type: 'success', message: 'Photo supprimée.');
    }

    public function openDocumentModal(int $missionId): void
    {
        $this->documentMissionId = $missionId;
        $this->documents = [];
        $this->document_type = 'autre';
        $this->showDocumentModal = true;
    }

    public function saveDocuments(): void
    {
        $this->validate([
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:10240', // 10MB max
        ]);

        $mission = Mission::findOrFail($this->documentMissionId);

        if ($this->documents) {
            foreach ($this->documents as $document) {
                MissionDocument::storeUpload($mission, $document, $this->document_type);
            }
        }

        $this->dispatch('notify', type: 'success', message: 'Documents enregistrés.');
        $this->showDocumentModal = false;
        $this->documents = [];
    }

    public function deleteDocument(int $documentId): void
    {
        $document = MissionDocument::findOrFail($documentId);
        $document->delete();
        $this->dispatch('notify', type: 'success', message: 'Document supprimé.');
    }

    public function openReportModal(): void
    {
        $this->report_start_date = now()->startOfMonth()->format('Y-m-d');
        $this->report_end_date = now()->endOfMonth()->format('Y-m-d');
        $this->report_status = '';
        $this->report_demandeur_id = null;
        $this->showReportModal = true;
    }

    public function exportReport()
    {
        $this->validate([
            'report_start_date' => 'required|date',
            'report_end_date' => 'required|date|after_or_equal:report_start_date',
        ]);

        $filename = 'rapport-missions-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(
            new MissionReportExport(
                $this->report_start_date,
                $this->report_end_date,
                $this->report_status ?: null,
                $this->report_demandeur_id
            ),
            $filename
        );
    }

    public function getCalendarMissionsProperty(): \Illuminate\Support\Collection
    {
        [$y, $m] = explode('-', $this->month_calendar);
        $start = \Carbon\Carbon::createFromDate((int) $y, (int) $m, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        return Mission::query()
            ->with(['vehicle:id,registration', 'driver:id,first_name,last_name', 'demandeur:id,name'])
            ->whereBetween('date_start', [$start, $end])
            ->orWhereBetween('date_end', [$start, $end])
            ->orderBy('date_start')
            ->get();
    }

    public function render(): View
    {
        $query = Mission::query()->with(['vehicle:id,registration', 'driver:id,first_name,last_name', 'demandeur:id,name']);
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->whereHas('vehicle', fn ($q2) => $q2->where('registration', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('demandeur', fn ($q2) => $q2->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhere('destination', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }
        $missions = $query->orderByDesc('date_start')->paginate(12);
        $vehicles = Vehicle::query()
            ->where(fn ($q) => $q->where('status', Vehicle::STATUS_AVAILABLE)->orWhere('id', $this->vehicle_id))
            ->orderBy('registration')
            ->get(['id', 'registration']);
        $drivers = Driver::query()
            ->where(fn ($q) => $q->where('is_available', true)->orWhere('id', $this->driver_id))
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);
        $demandeurs = Demandeur::orderBy('name')->get(['id', 'name']);
        $cities = \App\Models\City::active()->orderBy('name')->get(['id', 'name', 'region']);

        return view('livewire.portal.missions.index', [
            'missions' => $missions,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'demandeurs' => $demandeurs,
            'cities' => $cities,
        ])->layout('layouts.app', ['title' => 'Planning missions']);
    }
}
