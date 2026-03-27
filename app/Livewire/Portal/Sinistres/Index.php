<?php

namespace App\Livewire\Portal\Sinistres;

use App\Models\Garage;
use App\Models\Mission;
use App\Models\Sinistre;
use App\Models\SinistrePhoto;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $status_filter = '';
    public bool $showFormModal = false;
    public bool $showPhotoModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $photoSinistreId = null;

    public ?int $vehicle_id = null;
    public ?int $mission_id = null;
    public string $declared_at = '';
    public string $description = '';
    public string $location = '';
    public string $estimated_cost = '';
    public string $responsibility = '';
    public ?int $garage_id = null;
    public ?int $assureur_id = null;
    public string $status = Sinistre::STATUS_DECLARED;
    public string $notes = '';
    public $photo_file = null;
    public $police_report_file = null;
    public ?int $driver_id = null;

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => '']];

    public function mount(): void
    {
        if ($this->declared_at === '') {
            $this->declared_at = now()->format('Y-m-d\TH:i');
        }
    }

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'mission_id' => 'nullable|exists:missions,id',
            'declared_at' => 'required|date',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'responsibility' => 'nullable|string|max:100',
            'garage_id' => 'nullable|exists:garages,id',
            'assureur_id' => 'nullable|exists:assureurs,id',
            'status' => 'required|in:declared,in_repair,closed',
            'notes' => 'nullable|string',
            'police_report_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'driver_id' => 'nullable|exists:drivers,id',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->declared_at = now()->format('Y-m-d\TH:i');
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $s = Sinistre::findOrFail($id);
        $this->editingId = $s->id;
        $this->vehicle_id = $s->vehicle_id;
        $this->mission_id = $s->mission_id;
        $this->declared_at = $s->declared_at->format('Y-m-d\TH:i');
        $this->description = $s->description;
        $this->location = $s->location ?? '';
        $this->estimated_cost = $s->estimated_cost ? format_money($s->estimated_cost, 2) : '';
        $this->responsibility = $s->responsibility ?? '';
        $this->garage_id = $s->garage_id;
        $this->assureur_id = $s->assureur_id;
        $this->status = $s->status;
        $this->notes = $s->notes ?? '';
        $this->showFormModal = true;
        $this->driver_id = $s->driver_id;
    }

    public function saveSinistre(): void
    {
        $this->estimated_cost = parse_french_number($this->estimated_cost) ?? $this->estimated_cost;
        $this->validate();
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'mission_id' => $this->mission_id,
            'declared_at' => $this->declared_at,
            'description' => $this->description,
            'location' => $this->location ?: null,
            'estimated_cost' => $this->estimated_cost ?: null,
            'responsibility' => $this->responsibility ?: null,
            'garage_id' => $this->garage_id,
            'assureur_id' => $this->assureur_id,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
            'driver_id' => $this->driver_id,
        ];
        if ($this->police_report_file) {
            $data['police_report_path'] = $this->police_report_file->store('sinistres/police-reports', 'public');
        }
        if ($this->editingId) {
            Sinistre::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Sinistre mis à jour.');
        } else {
            Sinistre::create($data);
            $this->dispatch('notify', type: 'success', message: 'Sinistre déclaré.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function openPhotoModal(int $id): void
    {
        $this->photoSinistreId = $id;
        $this->photo_file = null;
        $this->showPhotoModal = true;
    }

    public function uploadPhoto(): void
    {
        $this->validate(['photo_file' => 'required|image|max:5120']);
        $sinistre = Sinistre::findOrFail($this->photoSinistreId);
        SinistrePhoto::storeUpload($sinistre, $this->photo_file);
        $this->dispatch('notify', type: 'success', message: 'Photo ajoutée.');
        $this->photo_file = null;
    }

    public function deletePhoto(int $id): void
    {
        SinistrePhoto::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Photo supprimée.');
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteSinistre(): void
    {
        if ($this->editingId) {
            Sinistre::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Sinistre supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->vehicle_id = null;
        $this->mission_id = null;
        $this->description = '';
        $this->location = '';
        $this->estimated_cost = '';
        $this->responsibility = '';
        $this->garage_id = null;
        $this->assureur_id = null;
        $this->status = Sinistre::STATUS_DECLARED;
        $this->notes = '';
        $this->police_report_file = null;
        $this->driver_id = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Sinistre::query()->with(['vehicle:id,registration', 'mission:id', 'garage:id,name', 'assureur:id,name','driver:id,first_name,last_name',]);
        if ($this->search !== '') {
            $query->where('description', 'like', '%' . $this->search . '%');
        }
        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }
        $sinistres = $query->orderByDesc('declared_at')->paginate(12);
        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);
        $missions = Mission::orderByDesc('date_start')->get(['id', 'date_start', 'vehicle_id']);
        $garages = Garage::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $assureurs = \App\Models\Assureur::orderBy('name')->get(['id', 'name']);
        $photoSinistre = $this->photoSinistreId ? Sinistre::with('photos')->find($this->photoSinistreId) : null;
        $drivers = \App\Models\Driver::orderBy('last_name')
    ->orderBy('first_name')
    ->get(['id', 'first_name', 'last_name', 'matricule']);

        return view('livewire.portal.sinistres.index', [
            'sinistres' => $sinistres,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'missions' => $missions,
            'garages' => $garages,
            'assureurs' => $assureurs,
            'photoSinistre' => $photoSinistre,
        ])->layout('layouts.app', ['title' => 'Sinistres']);
    }
}
