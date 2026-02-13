<?php

namespace App\Livewire\Portal\Diagnostics;

use App\Models\Diagnostic;
use App\Models\Vehicle;
use App\Models\Mechanic;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status_filter = '';
    public string $date_filter = '';
    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $vehicle_id = null;
    public ?int $mechanic_id = null;
    public string $diagnostic_date = '';
    public string $user_name = '';
    public string $user_role = '';
    public string $km_arrival = '';
    public string $observations = '';
    public string $engine_issues = '';
    public string $suspension_transmission = '';
    public string $braking_system = '';
    public string $electronics_electricity = '';
    public string $bodywork_paint = '';
    public string $air_conditioning = '';
    public string $other_issues = '';
    public string $internal_works = '';
    public string $external_works = '';
    public string $conclusion = '';

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => ''], 'date_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'mechanic_id' => 'nullable|exists:mechanics,id',
            'diagnostic_date' => 'required|date',
            'user_name' => 'required|string|max:200',
            'user_role' => 'required|string|max:100',
            'km_arrival' => 'required|integer|min:0',
            'observations' => 'nullable|string',
            'engine_issues' => 'nullable|string',
            'suspension_transmission' => 'nullable|string',
            'braking_system' => 'nullable|string',
            'electronics_electricity' => 'nullable|string',
            'bodywork_paint' => 'nullable|string',
            'air_conditioning' => 'nullable|string',
            'other_issues' => 'nullable|string',
            'internal_works' => 'nullable|string',
            'external_works' => 'nullable|string',
            'conclusion' => 'nullable|string',
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
        $diagnostic = Diagnostic::findOrFail($id);
        $this->editingId = $diagnostic->id;
        $this->vehicle_id = $diagnostic->vehicle_id;
        $this->mechanic_id = $diagnostic->mechanic_id;
        $this->diagnostic_date = $diagnostic->diagnostic_date->format('Y-m-d');
        $this->user_name = $diagnostic->user_name;
        $this->user_role = $diagnostic->user_role;
        $this->km_arrival = (string) $diagnostic->km_arrival;
        $this->observations = $diagnostic->observations ?? '';
        $this->engine_issues = $diagnostic->engine_issues ?? '';
        $this->suspension_transmission = $diagnostic->suspension_transmission ?? '';
        $this->braking_system = $diagnostic->braking_system ?? '';
        $this->electronics_electricity = $diagnostic->electronics_electricity ?? '';
        $this->bodywork_paint = $diagnostic->bodywork_paint ?? '';
        $this->air_conditioning = $diagnostic->air_conditioning ?? '';
        $this->other_issues = $diagnostic->other_issues ?? '';
        $this->internal_works = $diagnostic->internal_works ?? '';
        $this->external_works = $diagnostic->external_works ?? '';
        $this->conclusion = $diagnostic->conclusion ?? '';
        $this->showFormModal = true;
    }

    public function saveDiagnostic(): void
    {
        $this->validate();
        
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'mechanic_id' => $this->mechanic_id,
            'diagnostic_date' => $this->diagnostic_date,
            'user_name' => $this->user_name,
            'user_role' => $this->user_role,
            'km_arrival' => (int) $this->km_arrival,
            'observations' => $this->observations ?: null,
            'engine_issues' => $this->engine_issues ?: null,
            'suspension_transmission' => $this->suspension_transmission ?: null,
            'braking_system' => $this->braking_system ?: null,
            'electronics_electricity' => $this->electronics_electricity ?: null,
            'bodywork_paint' => $this->bodywork_paint ?: null,
            'air_conditioning' => $this->air_conditioning ?: null,
            'other_issues' => $this->other_issues ?: null,
            'internal_works' => $this->internal_works ?: null,
            'external_works' => $this->external_works ?: null,
            'conclusion' => $this->conclusion ?: null,
        ];

        if ($this->editingId) {
            $diagnostic = Diagnostic::findOrFail($this->editingId);
            $diagnostic->update($data);
            $this->dispatch('notify', type: 'success', message: 'Diagnostic mis à jour.');
        } else {
            $data['reference'] = Diagnostic::generateReference();
            Diagnostic::create($data);
            $this->dispatch('notify', type: 'success', message: 'Diagnostic créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDiagnostic(): void
    {
        if ($this->editingId) {
            Diagnostic::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Diagnostic supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function createWorkOrder(int $diagnosticId): void
    {
        // Rediriger vers la création de bon de travail avec le diagnostic pré-sélectionné
        $this->dispatch('navigate', route('work-orders.create', ['diagnostic_id' => $diagnosticId]));
    }

    public function viewDiagnostic(int $id): void
    {
        $this->editingId = $id;
        $diagnostic = Diagnostic::findOrFail($id);
        
        // Pré-remplir les champs pour la vue
        $this->vehicle_id = $diagnostic->vehicle_id;
        $this->mechanic_id = $diagnostic->mechanic_id;
        $this->diagnostic_date = $diagnostic->diagnostic_date->format('Y-m-d');
        $this->user_name = $diagnostic->user_name;
        $this->user_role = $diagnostic->user_role;
        $this->km_arrival = (string) $diagnostic->km_arrival;
        $this->observations = $diagnostic->observations ?? '';
        $this->engine_issues = $diagnostic->engine_issues ?? '';
        $this->suspension_transmission = $diagnostic->suspension_transmission ?? '';
        $this->braking_system = $diagnostic->braking_system ?? '';
        $this->electronics_electricity = $diagnostic->electronics_electricity ?? '';
        $this->bodywork_paint = $diagnostic->bodywork_paint ?? '';
        $this->air_conditioning = $diagnostic->air_conditioning ?? '';
        $this->other_issues = $diagnostic->other_issues ?? '';
        $this->internal_works = $diagnostic->internal_works ?? '';
        $this->external_works = $diagnostic->external_works ?? '';
        $this->conclusion = $diagnostic->conclusion ?? '';
        
        $this->showViewModal = true;
    }

    public function downloadPDF(int $id): void
    {
        $diagnostic = Diagnostic::findOrFail($id);
        
        // Générer le PDF et le télécharger
        $this->dispatch('download-diagnostic-pdf', id: $id);
    }

    private function resetForm(): void
    {
        $this->vehicle_id = null;
        $this->mechanic_id = null;
        $this->diagnostic_date = '';
        $this->user_name = '';
        $this->user_role = '';
        $this->km_arrival = '';
        $this->observations = '';
        $this->engine_issues = '';
        $this->suspension_transmission = '';
        $this->braking_system = '';
        $this->electronics_electricity = '';
        $this->bodywork_paint = '';
        $this->air_conditioning = '';
        $this->other_issues = '';
        $this->internal_works = '';
        $this->external_works = '';
        $this->conclusion = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Diagnostic::query()->with(['vehicle:id,registration', 'mechanic:id,first_name,last_name']);
        
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->whereHas('vehicle', fn ($q2) => $q2->where('registration', 'like', '%' . $this->search . '%'))
                    ->orWhere('reference', 'like', '%' . $this->search . '%')
                    ->orWhere('user_name', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->status_filter !== '') {
            // Filtrer par statut basé sur les work orders
            if ($this->status_filter === 'treated') {
                $query->whereHas('workOrders', fn ($q) => $q->where('status', 'completed'));
            } elseif ($this->status_filter === 'in_progress') {
                $query->whereHas('workOrders', fn ($q) => $q->where('status', 'in_progress'));
            } elseif ($this->status_filter === 'pending') {
                $query->whereDoesntHave('workOrders');
            }
        }
        
        if ($this->date_filter !== '') {
            $query->whereDate('diagnostic_date', $this->date_filter);
        }

        $diagnostics = $query->orderByDesc('diagnostic_date')->paginate(12);
        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);
        $mechanics = Mechanic::where('is_active', true)->orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        return view('livewire.portal.diagnostics.index', [
            'diagnostics' => $diagnostics,
            'vehicles' => $vehicles,
            'mechanics' => $mechanics,
        ])->layout('layouts.app', ['title' => 'Diagnostics']);
    }
}
