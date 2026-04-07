<?php

namespace App\Livewire\Portal\Diagnostics;

use App\Models\Diagnostic;
use App\Models\Driver;
use App\Models\Garage;
use App\Models\Person;
use App\Models\Vehicle;
use App\Models\Mechanic;
use App\Models\WorkOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Validation\Rule;
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
    public ?int $garage_id = null;
    public ?int $mechanic_id = null;
    public string $diagnostic_date = '';
    public string $requester_kind = 'driver';
    public ?int $requester_id = null;
    public string $user_name = '';
    public string $user_role = '';
    public string $km_arrival = '';
    public bool $has_admin_file = false;
    public bool $has_jack = false;
    public bool $has_wheel_key = false;
    public bool $has_spare_wheel = false;
    public bool $has_first_aid = false;
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

    public int|null $mileage = null;
    // État des systèmes (valeurs : '' | 'ok' | 'defaillant' | 'a_surveiller')
    public string $system_engine     = '';
    public string $system_suspension = '';
    public string $system_electrical = '';
    public string $system_body       = '';
    public string $system_ac         = '';
    // Classification
    public string $failure_cause         = '';
    public string $failure_cause_comment = '';
    public string $failure_type          = '';
    public string $maintenance_type      = '';
    public string $operation_type        = '';


    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => ''], 'date_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'garage_id' => 'nullable|exists:garages,id',
            'mechanic_id' => 'nullable|exists:mechanics,id',
            'diagnostic_date' => 'required|date',
            'requester_kind' => 'required|in:driver,person',
            'requester_id' => 'nullable|integer',
            'user_name' => 'nullable|string|max:200',
            'user_role' => 'nullable|string|max:100',
            'km_arrival' => 'required|integer|min:0',
            'has_admin_file' => 'boolean',
            'has_jack' => 'boolean',
            'has_wheel_key' => 'boolean',
            'has_spare_wheel' => 'boolean',
            'has_first_aid' => 'boolean',
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
            'mileage'           => ['nullable', 'integer', 'min:0'],
            'system_engine'     => ['nullable', 'string'],
            'system_suspension' => ['nullable', 'string'],
            'system_electrical' => ['nullable', 'string'],
            'system_body'       => ['nullable', 'string'],
            'system_ac'         => ['nullable', 'string'],
            'failure_cause'     => ['nullable', Rule::in(array_keys(\App\Models\WorkOrder::failureCauses()))],
            'failure_type'      => ['nullable', Rule::in(array_keys(\App\Models\WorkOrder::failureTypes()))],
            'maintenance_type'  => ['nullable', Rule::in(array_keys(\App\Models\WorkOrder::maintenanceTypes()))],
            'operation_type'    => ['nullable', Rule::in(array_keys(\App\Models\WorkOrder::operationTypes()))],
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
        $this->garage_id = $diagnostic->garage_id;
        $this->mechanic_id = $diagnostic->mechanic_id;
        $this->diagnostic_date = $diagnostic->diagnostic_date->format('Y-m-d');
        $this->requester_kind = $diagnostic->requester_type === Person::class ? 'person' : 'driver';
        $this->requester_id = $diagnostic->requester_id;
        $this->user_name = $diagnostic->user_name;
        $this->user_role = $diagnostic->user_role;
        $this->km_arrival = (string) $diagnostic->km_arrival;
        $this->has_admin_file = (bool) $diagnostic->has_admin_file;
        $this->has_jack = (bool) $diagnostic->has_jack;
        $this->has_wheel_key = (bool) $diagnostic->has_wheel_key;
        $this->has_spare_wheel = (bool) $diagnostic->has_spare_wheel;
        $this->has_first_aid = (bool) $diagnostic->has_first_aid;
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

        $workOrder = $diagnostic->workOrders()->first();
        $this->mileage               = $workOrder?->mileage;
        $this->system_engine         = $workOrder?->system_engine ?? '';
        $this->system_suspension     = $workOrder?->system_suspension ?? '';
        $this->system_electrical     = $workOrder?->system_electrical ?? '';
        $this->system_body           = $workOrder?->system_body ?? '';
        $this->system_ac             = $workOrder?->system_ac ?? '';
        $this->failure_cause         = $workOrder?->failure_cause ?? '';
        $this->failure_cause_comment = $workOrder?->failure_cause_comment ?? '';
        $this->failure_type          = $workOrder?->failure_type ?? '';
        $this->maintenance_type      = $workOrder?->maintenance_type ?? '';
        $this->operation_type        = $workOrder?->operation_type ?? '';
    }

    public function saveDiagnostic(): void
    {
        $this->validate();
        $mechanic = Mechanic::where('user_id', Auth::id())->first();
        $requesterClass = $this->requester_kind === 'person' ? Person::class : Driver::class;
        $requester = $this->requester_id ? $requesterClass::find($this->requester_id) : null;
        $resolvedName = $requester?->full_name ?? $requester?->name ?? $this->user_name;
        $resolvedRole = $this->requester_kind === 'person' ? 'Personne' : 'Chauffeur';
        
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'garage_id' => $this->garage_id,
            'mechanic_id' => $mechanic?->id ?: $this->mechanic_id,
            'diagnostic_date' => $this->diagnostic_date,
            'requester_type' => $requesterClass,
            'requester_id' => $this->requester_id,
            'user_name' => $resolvedName,
            'user_role' => $resolvedRole,
            'km_arrival' => (int) $this->km_arrival,
            'has_admin_file' => $this->has_admin_file,
            'has_jack' => $this->has_jack,
            'has_wheel_key' => $this->has_wheel_key,
            'has_spare_wheel' => $this->has_spare_wheel,
            'has_first_aid' => $this->has_first_aid,
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
            $diagnostic = Diagnostic::create($data);
            $targetMechanicId = $diagnostic->mechanic_id ?: Mechanic::where('is_active', true)->value('id');
            if ($targetMechanicId) {
                WorkOrder::firstOrCreate(
                    ['diagnostic_id' => $diagnostic->id],
                    [
                        'vehicle_id' => $diagnostic->vehicle_id,
                        'mechanic_id' => $targetMechanicId,
                        'reference' => WorkOrder::generateReference(),
                        'transfer_reference' => 'TR-' . $diagnostic->reference,
                        'transfer_date' => $diagnostic->diagnostic_date,
                        'work_date' => $diagnostic->diagnostic_date,
                        'work_description' => trim((string) ($diagnostic->internal_works ?: $diagnostic->external_works ?: 'Travaux à compléter à partir du pré-diagnostic.')),
                        'status' => WorkOrder::STATUS_PENDING,
                        'completion_percent' => 0,

                        'mileage'               => $this->mileage,
                        'system_engine'         => $this->system_engine       ?: null,
                        'system_suspension'     => $this->system_suspension   ?: null,
                        'system_electrical'     => $this->system_electrical   ?: null,
                        'system_body'           => $this->system_body         ?: null,
                        'system_ac'             => $this->system_ac           ?: null,
                        'failure_cause'         => $this->failure_cause       ?: null,
                        'failure_cause_comment' => $this->failure_cause_comment ?: null,
                        'failure_type'          => $this->failure_type        ?: null,
                        'maintenance_type'      => $this->maintenance_type    ?: null,
                        'operation_type'        => $this->operation_type      ?: null,
                    ]
                );
            }
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
        $this->garage_id = null;
        $this->mechanic_id = null;
        $this->diagnostic_date = '';
        $this->requester_kind = 'driver';
        $this->requester_id = null;
        $this->user_name = '';
        $this->user_role = '';
        $this->km_arrival = '';
        $this->has_admin_file = false;
        $this->has_jack = false;
        $this->has_wheel_key = false;
        $this->has_spare_wheel = false;
        $this->has_first_aid = false;
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
        $query = Diagnostic::query()->with(['vehicle:id,registration', 'garage:id,name', 'mechanic:id,first_name,last_name']);
        
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
        $garages = Garage::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $mechanics = Mechanic::where('is_active', true)->orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $drivers = Driver::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $persons = Person::orderBy('name')->get(['id', 'name', 'first_name', 'last_name']);

        return view('livewire.portal.diagnostics.index', [
            'diagnostics' => $diagnostics,
            'vehicles' => $vehicles,
            'garages' => $garages,
            'mechanics' => $mechanics,
            'drivers' => $drivers,
            'persons' => $persons,
        ])->layout('layouts.app', ['title' => 'Diagnostics']);
    }
}
