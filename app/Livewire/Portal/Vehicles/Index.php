<?php

namespace App\Livewire\Portal\Vehicles;

use App\Models\Brand;
use App\Models\Direction;
use App\Models\Garage;
use App\Models\Vehicle;
use App\Models\VehicleCarteGrise;
use App\Models\VehicleDocument;
use App\Models\VehicleModel;
use App\Models\VehiclePhoto;
use App\Models\Person;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Department;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $status_filter = '';
    public string $view_mode = 'table'; // 'table' | 'card'

    public bool $showFormModal = false;
    public bool $showDocModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $docVehicleId = null;

    public string $registration = '';
    public ?int $brand_id = null;
    public ?int $vehicle_model_id = null;
    public string $category = '';
    public string $purchase_date = '';
    public string $purchase_price = '';
    public string $mileage = '0';
    public string $power = '';
    public string $status = Vehicle::STATUS_AVAILABLE;
    public ?int $garage_id = null;
    public ?int $assigned_person_id = null;
    public string $assignment_type = '';
    public bool $assignment_period_indefinite = true;
    public string $assignment_start_at = '';
    public string $assignment_end_at = '';
    public string $notes = '';

    public string $doc_type = VehicleDocument::TYPE_ASSURANCE;
    public $doc_file = null;
    public string $doc_expires_at = '';

    public $photo_file = null;
    public string $photo_caption = '';
    public string $photo_taken_at = '';

    public string $cg_reference_number = '';
    public string $cg_issued_at = '';
    public string $cg_expires_at = '';
    public $cg_file = null;
    public string $cg_notes = '';

    public bool $showQuickAddPerson = false;
    public string $quick_name = '';
    public string $quick_email = '';
    public string $quick_phone = '';
    public ?int $quick_direction_id = null;
    public ?int $quick_department_id = null;

    public bool $showQuickAddDirection = false;
    public string $quick_direction_name = '';
    public string $quick_direction_code = '';


    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        $unique = $this->editingId
            ? 'required|string|max:20|unique:vehicles,registration,' . $this->editingId
            : 'required|string|max:20|unique:vehicles,registration';
        return [
            'registration' => $unique,
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'category' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'mileage' => 'nullable|integer|min:0',
            'power' => 'nullable|integer|min:0',
            'status' => 'required|in:available,in_use,repair,out_of_service',
            'garage_id' => 'nullable|exists:garages,id',
            'assigned_person_id' => 'nullable|exists:persons,id',
            'assignment_type' => 'nullable|string|in:dotation,affectation,liaison,lucatelli,sec_surete,travaux,missions,transport_vip',
            'assignment_start_at' => 'nullable|date',
            'assignment_end_at' => 'nullable|date|after_or_equal:assignment_start_at',
            'notes' => 'nullable|string',
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
        $v = Vehicle::with('vehicleModel.brand')->findOrFail($id);
        $this->editingId = $v->id;
        $this->registration = $v->registration;
        $this->vehicle_model_id = $v->vehicle_model_id;
        $this->brand_id = $v->vehicleModel?->brand_id;
        $this->category = $v->category ?? '';
        $this->purchase_date = $v->purchase_date?->format('Y-m-d') ?? '';
        $this->purchase_price = $v->purchase_price ? format_money($v->purchase_price, 2) : '';
        $this->mileage = (string) $v->mileage;
        $this->power = $v->power !== null ? (string) $v->power : '';
        $this->status = $v->status;
        $this->garage_id = $v->garage_id;
        $this->assigned_person_id = $v->assigned_person_id;
        $this->assignment_type = $v->assignment_type ?? '';
        $this->assignment_period_indefinite = $v->assignment_end_at === null;
        $this->assignment_start_at = $v->assignment_start_at?->format('Y-m-d') ?? '';
        $this->assignment_end_at = $v->assignment_end_at?->format('Y-m-d') ?? '';
        $this->notes = $v->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveVehicle(): void
    {
        $this->purchase_price = parse_french_number($this->purchase_price) ?? $this->purchase_price;
        $this->validate();
        $data = [
            'registration' => $this->registration,
            'vehicle_model_id' => $this->vehicle_model_id,
            'category' => $this->category ?: null,
            'purchase_date' => $this->purchase_date ?: null,
            'purchase_price' => $this->purchase_price ?: null,
            'mileage' => (int) $this->mileage,
            'power' => $this->power !== '' ? (int) $this->power : null,
            'status' => $this->status,
            'garage_id' => $this->garage_id,
            'assigned_person_id' => $this->assigned_person_id,
            'assignment_type' => $this->assignment_type ?: null,
            'assignment_start_at' => $this->assignment_start_at ?: null,
            'assignment_end_at' => $this->assignment_period_indefinite ? null : ($this->assignment_end_at ?: null),
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            $vehicle = Vehicle::findOrFail($this->editingId);
            $vehicle->update($data);
            $this->dispatch('notify', type: 'success', message: 'Véhicule mis à jour.');
        } else {
            $vehicle = Vehicle::create($data);
            $this->dispatch('notify', type: 'success', message: 'Véhicule créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['table', 'card'], true)) {
            $this->view_mode = $mode;
        }
    }

    public function openDocModal(int $vehicleId): void
    {
        $this->docVehicleId = $vehicleId;
        $this->doc_type = VehicleDocument::TYPE_ASSURANCE;
        $this->doc_file = null;
        $this->doc_expires_at = '';
        $this->photo_file = null;
        $this->photo_caption = '';
        $this->photo_taken_at = '';
        $this->cg_reference_number = '';
        $this->cg_issued_at = '';
        $this->cg_expires_at = '';
        $this->cg_file = null;
        $this->cg_notes = '';
        $this->showDocModal = true;
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'doc_type' => 'required|in:assurance,carte_grise,autre',
            'doc_file' => 'required|file|max:10240', // 10 Mo
            'doc_expires_at' => 'nullable|date',
        ]);
        $vehicle = Vehicle::findOrFail($this->docVehicleId);
        VehicleDocument::storeUpload($vehicle, $this->doc_file, $this->doc_type, $this->doc_expires_at ?: null);
        $this->dispatch('notify', type: 'success', message: 'Document ajouté.');
        $this->doc_file = null;
        $this->doc_expires_at = '';
    }

    public function deleteDocument(int $id): void
    {
        VehicleDocument::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Document supprimé.');
    }

    public function uploadPhoto(): void
    {
        $this->validate([
            'photo_file' => 'required|image|max:5120',
            'photo_caption' => 'nullable|string|max:255',
            'photo_taken_at' => 'nullable|date',
        ]);
        $vehicle = Vehicle::findOrFail($this->docVehicleId);
        VehiclePhoto::storeUpload($vehicle, $this->photo_file, $this->photo_caption ?: null, $this->photo_taken_at ?: null);
        $this->dispatch('notify', type: 'success', message: 'Photo ajoutée.');
        $this->photo_file = null;
        $this->photo_caption = '';
        $this->photo_taken_at = '';
    }

    public function deletePhoto(int $id): void
    {
        VehiclePhoto::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Photo supprimée.');
    }

    public function addCarteGrise(): void
    {
        $this->validate([
            'cg_reference_number' => 'nullable|string|max:100',
            'cg_issued_at' => 'nullable|date',
            'cg_expires_at' => 'nullable|date',
            'cg_file' => 'nullable|file|max:10240',
            'cg_notes' => 'nullable|string',
        ]);
        $vehicle = Vehicle::findOrFail($this->docVehicleId);
        VehicleCarteGrise::createForVehicle($vehicle, [
            'reference_number' => $this->cg_reference_number ?: null,
            'issued_at' => $this->cg_issued_at ?: null,
            'expires_at' => $this->cg_expires_at ?: null,
            'notes' => $this->cg_notes ?: null,
        ], $this->cg_file);
        $this->dispatch('notify', type: 'success', message: 'Carte grise enregistrée.');
        $this->cg_reference_number = '';
        $this->cg_issued_at = '';
        $this->cg_expires_at = '';
        $this->cg_file = null;
        $this->cg_notes = '';
    }

    public function deleteCarteGrise(int $id): void
    {
        VehicleCarteGrise::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Carte grise supprimée.');
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteVehicle(): void
    {
        if ($this->editingId) {
            Vehicle::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Véhicule supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function closeDocModal(): void
    {
        $this->showDocModal = false;
        $this->docVehicleId = null;
    }

    public function getComputedVenalValueProperty(): ?string
    {
        $rawPrice = parse_french_number($this->purchase_price);
        if (!$rawPrice || !$this->purchase_date) {
            return null;
        }
        $v = new Vehicle([
            'purchase_price' => $rawPrice,
            'purchase_date' => $this->purchase_date,
        ]);
        $v->purchase_date = \Carbon\Carbon::parse($this->purchase_date);
        $val = $v->computeVenalValue();
        return $val !== null ? format_money($val, 2) : null;
    }

    public function updatedBrandId(): void
    {
        $this->vehicle_model_id = null;
    }

    public function updatedAssignmentType(): void
    {
        if (in_array($this->assignment_type, [Vehicle::ASSIGNMENT_DOTATION], true)) {
            $this->assignment_period_indefinite = true;
        }
    }

    public function openQuickAddPerson(): void
    {
        $this->quick_name = '';
        $this->quick_email = '';
        $this->quick_phone = '';
        $this->quick_direction_id = null;
        $this->quick_department_id = null;
        $this->showQuickAddPerson = true;
    }

    public function closeQuickAddPerson(): void
    {
        $this->showQuickAddPerson = false;
        $this->resetValidation(['quick_name', 'quick_email', 'quick_direction_id', 'quick_department_id']);
    }

    public function updatedQuickDirectionId(): void
    {
        $this->quick_department_id = null;
    }

    public function saveQuickPerson(): void
    {
        $this->validate([
            'quick_name' => 'required|string|max:150',
            'quick_email' => 'nullable|email|max:150',
            'quick_phone' => 'nullable|string|max:30',
            'quick_direction_id' => 'nullable|exists:directions,id',
            'quick_department_id' => 'nullable|exists:departments,id',
        ]);
        $parts = preg_split('/\s+/', trim($this->quick_name)) ?: [];
        $person = Person::create([
            'name' => $this->quick_name,
            'first_name' => $parts[0] ?? null,
            'last_name' => count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null,
            'email' => $this->quick_email ?: null,
            'phone' => $this->quick_phone ?: null,
            'direction_id' => $this->quick_direction_id ?: null,
            'department_id' => $this->quick_department_id ?: null,
        ]);
        $this->assigned_person_id = $person->id;
        $this->showQuickAddPerson = false;
        $this->quick_name = '';
        $this->quick_email = '';
        $this->quick_phone = '';
        $this->quick_direction_id = null;
        $this->quick_department_id = null;
        $this->dispatch('notify', type: 'success', message: 'Personne ajoutée et sélectionnée.');
    }

    public function openQuickAddDirection(): void
    {
        $this->quick_direction_name = '';
        $this->quick_direction_code = '';
        $this->showQuickAddDirection = true;
    }

    public function closeQuickAddDirection(): void
    {
        $this->showQuickAddDirection = false;
        $this->resetValidation(['quick_direction_name', 'quick_direction_code']);
    }

    public function saveQuickDirection(): void
    {
        $this->validate([
            'quick_direction_name' => 'required|string|max:150',
            'quick_direction_code' => 'nullable|string|max:50',
        ]);
        $direction = Direction::create([
            'name' => $this->quick_direction_name,
            'code' => $this->quick_direction_code ?: null,
        ]);
        $this->quick_direction_id = $direction->id;
        $this->quick_department_id = null;
        $this->showQuickAddDirection = false;
        $this->quick_direction_name = '';
        $this->quick_direction_code = '';
        $this->dispatch('notify', type: 'success', message: 'Direction ajoutée et sélectionnée.');
    }

    private function resetForm(): void
    {
        $this->registration = '';
        $this->brand_id = null;
        $this->vehicle_model_id = null;
        $this->category = '';
        $this->purchase_date = '';
        $this->purchase_price = '';
        $this->mileage = '0';
        $this->power = '';
        $this->status = Vehicle::STATUS_AVAILABLE;
        $this->garage_id = null;
        $this->assigned_person_id = null;
        $this->assignment_type = '';
        $this->assignment_period_indefinite = true;
        $this->assignment_start_at = '';
        $this->assignment_end_at = '';
        $this->notes = '';
        $this->showQuickAddDirection = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Vehicle::query()->with(['vehicleModel.brand:id,name', 'garage:id,name', 'assignedPerson:id,name']);
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('registration', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicleModel', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhereHas('brand', fn ($q3) => $q3->where('name', 'like', '%' . $this->search . '%'));
                    });
            });
        }
        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }
        $vehicles = $query->with(['photos'])->orderBy('registration')->paginate(12);
        $garages = Garage::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $brands = Brand::orderBy('name')->get(['id', 'name']);
        $vehicleModelsForBrand = $this->brand_id
            ? VehicleModel::where('brand_id', $this->brand_id)->orderBy('name')->get(['id', 'name', 'brand_id'])
            : collect();
        $persons = Person::orderBy('name')->get(['id', 'name']);
        $docVehicle = $this->docVehicleId ? Vehicle::with(['documents', 'photos', 'carteGrises'])->find($this->docVehicleId) : null;
        $directions = Direction::orderBy('name')->get(['id', 'name']);
        $departments = Department::when($this->quick_direction_id, fn ($q) => $q->where('direction_id', $this->quick_direction_id))
            ->orderBy('name')
            ->get(['id', 'name', 'direction_id']);

        return view('livewire.portal.vehicles.index', [
            'vehicles' => $vehicles,
            'garages' => $garages,
            'brands' => $brands,
            'vehicleModelsForBrand' => $vehicleModelsForBrand,
            'persons' => $persons,
            'docVehicle' => $docVehicle,
            'directions' => $directions,
            'departments' => $departments,
        ])->layout('layouts.app', ['title' => 'Gestion des véhicules']);
    }
}
