<?php

namespace App\Livewire\Portal\Repairs;

use App\Models\Garage;
use App\Models\Repair;
use App\Models\RepairExpense;
use App\Models\RepairPhoto;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $type_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $vehicle_id = null;
    public ?int $garage_id = null;
    public string $type = Repair::TYPE_INTERNAL;
    public $transfer_sheet = null;
    public string $description = '';
    public string $cost = '';
    public string $started_at = '';
    public string $completed_at = '';
    public string $notes = '';
    // Prestation : délai donné au prestataire (date limite) + évaluation
    public string $expected_completed_at = '';
    public string $quality_rating = '';
    public string $delay_rating = '';
    public string $evaluation_comment = '';
    public array $expense_lines = [];
    public array $expense_files = [];

    // Photos avant/après réparation
    public bool $showPhotoModal = false;
    public ?int $photoRepairId = null;
    public string $repair_photo_type = 'before';
    public $repair_before_photos = [];
    public $repair_after_photos = [];
    
    // Nouvelle rubrique pour les réparations
    public string $repair_type = '';
    public string $priority = 'medium';
    public string $estimated_duration = '';
    public ?int $mechanic_id = null;

    protected $queryString = ['search' => ['except' => ''], 'type_filter' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'garage_id' => 'required|exists:garages,id',
            'type' => 'required|in:internal,external',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'repair_type' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'nullable|string|max:50',
            'mechanic_id' => 'nullable|exists:mechanics,id',
            'expected_completed_at' => 'nullable|date',
            'quality_rating' => 'nullable|numeric|min:1|max:5',
            'delay_rating' => 'nullable|numeric|min:1|max:5',
            'evaluation_comment' => 'nullable|string|max:2000',
        ];
        return $rules;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $r = Repair::findOrFail($id);
        $this->editingId = $r->id;
        $this->vehicle_id = $r->vehicle_id;
        $this->garage_id = $r->garage_id;
        $this->type = $r->type;
        $this->description = $r->description;
        $this->cost = $r->cost ? format_money($r->cost, 2) : '';
        $this->started_at = $r->started_at?->format('Y-m-d') ?? '';
        $this->completed_at = $r->completed_at?->format('Y-m-d') ?? '';
        $this->expected_completed_at = $r->expected_completed_at?->format('Y-m-d') ?? '';
        $this->quality_rating = $r->quality_rating !== null ? (string) $r->quality_rating : '';
        $this->delay_rating = $r->delay_rating !== null ? (string) $r->delay_rating : '';
        $this->evaluation_comment = $r->evaluation_comment ?? '';
        $this->expense_lines = $r->expenses->map(fn ($e) => [
            'label' => $e->label,
            'amount' => (string) $e->amount,
            'notes' => $e->notes ?? '',
            'existing_attachment_path' => $e->attachment_path,
            'existing_attachment_name' => $e->attachment_name,
        ])->toArray();
        $this->expense_files = [];
        $this->notes = $r->notes ?? '';
        $this->repair_type = $r->repair_type ?? '';
        $this->priority = $r->priority ?? 'medium';
        $this->estimated_duration = $r->estimated_duration ?? '';
        $this->mechanic_id = $r->mechanic_id;
        $this->showFormModal = true;
    }

    public function saveRepair(): void
    {
        $this->cost = parse_french_number($this->cost) ?? $this->cost;
        $this->validate();
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'garage_id' => $this->garage_id,
            'type' => $this->type,
            'description' => $this->description,
            'cost' => $this->cost ?: null,
            'started_at' => $this->started_at ?: null,
            'completed_at' => $this->completed_at ?: null,
            'expected_completed_at' => $this->expected_completed_at ?: null,
            'notes' => $this->notes ?: null,
            'repair_type' => $this->repair_type,
            'priority' => $this->priority,
            'estimated_duration' => $this->estimated_duration ?: null,
            'mechanic_id' => $this->mechanic_id ?: null,
        ];
        if ($this->quality_rating !== '' || $this->delay_rating !== '' || $this->evaluation_comment !== '') {
            $data['quality_rating'] = $this->quality_rating !== '' ? (float) $this->quality_rating : null;
            $data['delay_rating'] = $this->delay_rating !== '' ? (float) $this->delay_rating : null;
            $data['evaluation_comment'] = $this->evaluation_comment ?: null;
            $data['evaluated_at'] = now();
        }
        if ($this->type === Repair::TYPE_EXTERNAL && $this->transfer_sheet) {
            $vehicle = Vehicle::findOrFail($this->vehicle_id);
            $path = $this->transfer_sheet->store('repairs/transfer-sheets', 'public');
            $data['transfer_sheet_path'] = $path;
        }
        if ($this->editingId) {
            $repair = Repair::findOrFail($this->editingId);
            $repair->update($data);
            if ($this->type === Repair::TYPE_EXTERNAL) {
                $repair->expenses()->delete();
                foreach ($this->expense_lines as $i => $line) {
                    if (! filled($line['label'] ?? null) || ! filled($line['amount'] ?? null)) {
                        continue;
                    }
                    $attachmentPath = null;
                    $attachmentName = null;
                    if (isset($this->expense_files[$i]) && $this->expense_files[$i]) {
                        $attachmentPath = $this->expense_files[$i]->store('repairs/expenses', 'public');
                        $attachmentName = $this->expense_files[$i]->getClientOriginalName();
                    }
                    $repair->expenses()->create([
                        'label' => $line['label'],
                        'amount' => parse_french_number((string) $line['amount']) ?? (float) $line['amount'],
                        'notes' => $line['notes'] ?? null,
                        'attachment_path' => $attachmentPath ?: ($line['existing_attachment_path'] ?? null),
                        'attachment_name' => $attachmentName ?: ($line['existing_attachment_name'] ?? null),
                        'user_id' => Auth::id(),
                    ]);
                }
            }
            $this->dispatch('notify', type: 'success', message: 'Réparation mise à jour.');
        } else {
            $repair = Repair::create($data);
            if ($this->type === Repair::TYPE_EXTERNAL) {
                foreach ($this->expense_lines as $i => $line) {
                    if (! filled($line['label'] ?? null) || ! filled($line['amount'] ?? null)) {
                        continue;
                    }
                    $attachmentPath = null;
                    $attachmentName = null;
                    if (isset($this->expense_files[$i]) && $this->expense_files[$i]) {
                        $attachmentPath = $this->expense_files[$i]->store('repairs/expenses', 'public');
                        $attachmentName = $this->expense_files[$i]->getClientOriginalName();
                    }
                    $repair->expenses()->create([
                        'label' => $line['label'],
                        'amount' => parse_french_number((string) $line['amount']) ?? (float) $line['amount'],
                        'notes' => $line['notes'] ?? null,
                        'attachment_path' => $attachmentPath,
                        'attachment_name' => $attachmentName,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
            $this->dispatch('notify', type: 'success', message: 'Réparation enregistrée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRepair(): void
    {
        if ($this->editingId) {
            Repair::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Réparation supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->vehicle_id = null;
        $this->garage_id = null;
        $this->type = Repair::TYPE_INTERNAL;
        $this->transfer_sheet = null;
        $this->description = '';
        $this->cost = '';
        $this->started_at = '';
        $this->completed_at = '';
        $this->notes = '';
        $this->expected_completed_at = '';
        $this->quality_rating = '';
        $this->delay_rating = '';
        $this->evaluation_comment = '';
        $this->expense_lines = [['label' => '', 'amount' => '', 'notes' => '', 'existing_attachment_path' => null, 'existing_attachment_name' => null]];
        $this->expense_files = [];
        $this->repair_type = '';
        $this->priority = 'medium';
        $this->estimated_duration = '';
        $this->mechanic_id = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Repair::query()->with(['vehicle:id,registration', 'garage:id,name', 'expenses', 'photos']);
        if ($this->search !== '') {
            $query->whereHas('vehicle', fn ($q) => $q->where('registration', 'like', '%' . $this->search . '%'));
        }
        if ($this->type_filter !== '') {
            $query->where('type', $this->type_filter);
        }
        $repairs = $query->orderByDesc('created_at')->paginate(12);
        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);
        $garages = Garage::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $mechanics = \App\Models\Mechanic::where('is_active', true)->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'specialization']);

        return view('livewire.portal.repairs.index', [
            'repairs' => $repairs,
            'vehicles' => $vehicles,
            'garages' => $garages,
            'mechanics' => $mechanics,
        ])->layout('layouts.app', ['title' => 'Réparations']);
    }

    public function openPhotoModal(int $repairId): void
    {
        $this->photoRepairId = $repairId;
        $this->repair_before_photos = [];
        $this->repair_after_photos  = [];
        $this->showPhotoModal = true;
    }

    public function saveRepairPhotos(): void
    {
        $this->validate([
            'repair_before_photos.*' => 'image|max:8192',
            'repair_after_photos.*'  => 'image|max:8192',
        ]);

        $repair = Repair::findOrFail($this->photoRepairId);

        foreach ($this->repair_before_photos as $photo) {
            RepairPhoto::storeUpload($repair, $photo, 'before');
        }
        foreach ($this->repair_after_photos as $photo) {
            RepairPhoto::storeUpload($repair, $photo, 'after');
        }

        $this->dispatch('notify', type: 'success', message: 'Photos enregistrées.');
        $this->showPhotoModal = false;
        $this->repair_before_photos = [];
        $this->repair_after_photos  = [];
    }

    public function deleteRepairPhoto(int $photoId): void
    {
        $photo = RepairPhoto::findOrFail($photoId);
        if (file_exists(storage_path('app/public/' . $photo->file_path))) {
            unlink(storage_path('app/public/' . $photo->file_path));
        }
        $photo->delete();
        $this->dispatch('notify', type: 'success', message: 'Photo supprimée.');
    }

    public function addExpenseLine(): void
    {
        $this->expense_lines[] = ['label' => '', 'amount' => '', 'notes' => '', 'existing_attachment_path' => null, 'existing_attachment_name' => null];
    }

    public function removeExpenseLine(int $index): void
    {
        if (count($this->expense_lines) <= 1) {
            return;
        }
        array_splice($this->expense_lines, $index, 1);
    }
}
