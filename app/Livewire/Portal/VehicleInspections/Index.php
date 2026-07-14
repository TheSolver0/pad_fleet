<?php

namespace App\Livewire\Portal\VehicleInspections;

use App\Models\Vehicle;
use App\Models\VehicleInspection;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search        = '';
    public string $result_filter = '';
    public string $status_filter = '';

    public bool  $showFormModal   = false;
    public bool  $showDeleteModal = false;
    public ?int  $editingId       = null;

    // Formulaire
    public ?int   $vehicle_id         = null;
    public string $inspected_at       = '';
    public string $expires_at         = '';
    public string $result             = VehicleInspection::RESULT_ADMITTED;
    public string $control_center     = '';
    public string $cost               = '';
    public string $certificate_number = '';
    public string $notes              = '';
    public $document_file             = null;

    protected $queryString = [
        'search'        => ['except' => ''],
        'result_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
    ];
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingResultFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'vehicle_id'         => 'required|exists:vehicles,id',
            'inspected_at'       => 'required|date',
            'expires_at'         => 'required|date|after:inspected_at',
            'result'             => 'required|in:admitted,adjourned,refused',
            'control_center'     => 'nullable|string|max:255',
            'cost'               => 'nullable|numeric|min:0',
            'certificate_number' => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
            'document_file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->inspected_at = now()->format('Y-m-d');
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $i = VehicleInspection::findOrFail($id);
        $this->editingId         = $i->id;
        $this->vehicle_id        = $i->vehicle_id;
        $this->inspected_at      = $i->inspected_at->format('Y-m-d');
        $this->expires_at        = $i->expires_at->format('Y-m-d');
        $this->result            = $i->result;
        $this->control_center    = $i->control_center ?? '';
        $this->cost              = $i->cost ? number_format((float)$i->cost, 0, ',', ' ') : '';
        $this->certificate_number = $i->certificate_number ?? '';
        $this->notes             = $i->notes ?? '';
        $this->showFormModal     = true;
    }

    public function save(): void
    {
        $this->cost = str_replace([' ', '\u202f', ','], ['', '', '.'], $this->cost);
        $this->validate();

        $data = [
            'vehicle_id'         => $this->vehicle_id,
            'inspected_at'       => $this->inspected_at,
            'expires_at'         => $this->expires_at,
            'result'             => $this->result,
            'control_center'     => $this->control_center ?: null,
            'cost'               => $this->cost ?: null,
            'certificate_number' => $this->certificate_number ?: null,
            'notes'              => $this->notes ?: null,
            'updated_by'         => auth()->id(),
        ];

        if ($this->document_file) {
            $data['document_path'] = $this->document_file->store('inspections', 'public');
        }

        if ($this->editingId) {
            VehicleInspection::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Visite technique mise à jour.');
        } else {
            $data['created_by'] = auth()->id();
            VehicleInspection::create($data);
            $this->dispatch('notify', type: 'success', message: 'Visite technique enregistrée.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->editingId) {
            VehicleInspection::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Visite supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->editingId          = null;
        $this->vehicle_id         = null;
        $this->inspected_at       = '';
        $this->expires_at         = '';
        $this->result             = VehicleInspection::RESULT_ADMITTED;
        $this->control_center     = '';
        $this->cost               = '';
        $this->certificate_number = '';
        $this->notes              = '';
        $this->document_file      = null;
        $this->resetValidation();
    }

    public function getStats(): array
    {
        $all      = VehicleInspection::query();
        $total    = Vehicle::count();
        $withInsp = Vehicle::has('inspections')->count();

        return [
            'total'          => VehicleInspection::count(),
            'valid'          => (clone $all)->whereDate('expires_at', '>=', now())
                                            ->where('result', VehicleInspection::RESULT_ADMITTED)->count(),
            'expiring_soon'  => VehicleInspection::expiringSoon()->count(),
            'expired'        => VehicleInspection::expired()->count(),
            'pending'        => VehicleInspection::pending()->count(),
            'without'        => $total - $withInsp,
        ];
    }

    public function render(): View
    {
        $query = VehicleInspection::with(['vehicle:id,registration', 'createdBy:id,name'])
            ->orderByDesc('inspected_at');

        if ($this->search !== '') {
            $query->whereHas('vehicle', fn($q) =>
                $q->where('registration', 'like', '%'.$this->search.'%')
            )->orWhere('control_center', 'like', '%'.$this->search.'%')
             ->orWhere('certificate_number', 'like', '%'.$this->search.'%');
        }

        if ($this->result_filter !== '') {
            $query->where('result', $this->result_filter);
        }

        if ($this->status_filter === 'expired') {
            $query->expired();
        } elseif ($this->status_filter === 'expiring') {
            $query->expiringSoon();
        } elseif ($this->status_filter === 'pending') {
            $query->pending();
        } elseif ($this->status_filter === 'without') {
            // Afficher les véhicules sans visite — requête spéciale
            $query = Vehicle::doesntHave('inspections')
                ->orderBy('registration')
                ->paginate(15);

            return view('livewire.portal.vehicle-inspections.index', [
                'inspections'     => collect(),
                'vehiclesWithout' => $query,
                'vehicles'        => Vehicle::orderBy('registration')->get(['id', 'registration']),
                'stats'           => $this->getStats(),
                'modeWithout'     => true,
            ])->layout('layouts.app', ['title' => 'Visites techniques']);
        }

        return view('livewire.portal.vehicle-inspections.index', [
            'inspections'     => $query->paginate(15),
            'vehiclesWithout' => collect(),
            'vehicles'        => Vehicle::orderBy('registration')->get(['id', 'registration']),
            'stats'           => $this->getStats(),
            'modeWithout'     => false,
        ])->layout('layouts.app', ['title' => 'Visites techniques']);
    }
}