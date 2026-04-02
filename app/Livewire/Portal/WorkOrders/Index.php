<?php

namespace App\Livewire\Portal\WorkOrders;

use App\Models\WorkOrder;
use App\Models\WorkOrderTask;
use App\Models\WorkOrderPart;
use App\Models\Diagnostic;
use App\Models\Vehicle;
use App\Models\Mechanic;
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
    public string $status_filter = '';
    public string $date_filter = '';
    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public bool $showPhotosModal = false;
    public ?int $editingId = null;

    public ?int $vehicle_id = null;
    public ?int $diagnostic_id = null;
    public ?int $mechanic_id = null;
    public string $work_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $work_description = '';
    public string $parts_used = '';
    public string $parts_removed = '';
    public string $equipment_used = '';
    public string $tools_used = '';
    public string $technical_notes = '';
    public string $problems_found = '';
    public string $solutions_applied = '';
    public string $quality_control = '';
    public string $final_checks = '';
    public string $labor_cost = '';
    public string $parts_cost = '';
    public string $completion_notes = '';
    public string $status = WorkOrder::STATUS_PENDING;
    public string $transfer_reference = '';
    public string $transfer_date = '';
    public int $completion_percent = 0;
    public array $tasks = [];
    public array $parts_lines = [];
    public bool $stock_applied = false;

    public $before_photo_file = null;
    public $after_photo_file = null;
    public string $photo_caption = '';
    public string $photo_taken_at = '';

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => ''], 'date_filter' => ['except' => '']];

    public function mount(): void
    {
        $this->resetForm();
        $prefillDiagnosticId = request()->query('diagnostic_id');
        if ($prefillDiagnosticId) {
            $this->openCreate();
            $this->diagnostic_id = (int) $prefillDiagnosticId;
            $this->updatedDiagnosticId();
        }
    }

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'diagnostic_id' => 'nullable|exists:diagnostics,id',
            'mechanic_id' => 'required|exists:mechanics,id',
            'work_date' => 'required|date',
            'transfer_reference' => 'nullable|string|max:100',
            'transfer_date' => 'nullable|date',
            'work_description' => 'required|string',
            'parts_used' => 'nullable|string',
            'parts_removed' => 'nullable|string',
            'equipment_used' => 'nullable|string',
            'tools_used' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'problems_found' => 'nullable|string',
            'solutions_applied' => 'nullable|string',
            'quality_control' => 'nullable|string',
            'final_checks' => 'nullable|string',
            'labor_cost' => 'nullable|numeric|min:0',
            'parts_cost' => 'nullable|numeric|min:0',
            'completion_notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,validated',
            'completion_percent' => 'nullable|integer|min:0|max:100',
            'tasks' => 'array',
            'tasks.*.title' => 'nullable|string|max:500',
            'tasks.*.estimated_minutes' => 'nullable|integer|min:0',
            'tasks.*.mechanic_id' => 'nullable|exists:mechanics,id',
            'tasks.*.is_done' => 'boolean',
            'parts_lines' => 'array',
            'parts_lines.*.article_id' => 'nullable|exists:articles,id',
            'parts_lines.*.quantity' => 'nullable|integer|min:1',
            'parts_lines.*.stock_location' => 'nullable|in:main,garage',
            'before_photo_file' => 'nullable|image|max:5120',
            'after_photo_file' => 'nullable|image|max:5120',
            'photo_taken_at' => 'nullable|date',
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
        $workOrder = WorkOrder::findOrFail($id);
        $this->editingId = $workOrder->id;
        $this->vehicle_id = $workOrder->vehicle_id;
        $this->diagnostic_id = $workOrder->diagnostic_id;
        $this->mechanic_id = $workOrder->mechanic_id;
        $this->work_date = $workOrder->work_date->format('Y-m-d');
        $this->transfer_reference = $workOrder->transfer_reference ?? '';
        $this->transfer_date = $workOrder->transfer_date?->format('Y-m-d') ?? '';
        $this->start_time = $workOrder->start_time?->format('H:i') ?? '';
        $this->end_time = $workOrder->end_time?->format('H:i') ?? '';
        $this->work_description = $workOrder->work_description;
        $this->parts_used = $workOrder->parts_used ?? '';
        $this->parts_removed = $workOrder->parts_removed ?? '';
        $this->equipment_used = $workOrder->equipment_used ?? '';
        $this->tools_used = $workOrder->tools_used ?? '';
        $this->technical_notes = $workOrder->technical_notes ?? '';
        $this->problems_found = $workOrder->problems_found ?? '';
        $this->solutions_applied = $workOrder->solutions_applied ?? '';
        $this->quality_control = $workOrder->quality_control ?? '';
        $this->final_checks = $workOrder->final_checks ?? '';
        $this->labor_cost = $workOrder->labor_cost ? number_format($workOrder->labor_cost, 2, '.', '') : '';
        $this->parts_cost = $workOrder->parts_cost ? number_format($workOrder->parts_cost, 2, '.', '') : '';
        $this->completion_notes = $workOrder->completion_notes ?? '';
        $this->status = $workOrder->status;
        $this->completion_percent = (int) ($workOrder->completion_percent ?? 0);
        $this->tasks = $workOrder->tasks->map(fn ($t) => [
            'title' => $t->title,
            'estimated_minutes' => $t->estimated_minutes,
            'mechanic_id' => $t->mechanic_id,
            'is_done' => (bool) $t->is_done,
        ])->toArray();
        $this->parts_lines = $workOrder->parts->map(fn ($p) => [
            'article_id' => $p->article_id,
            'quantity' => (string) $p->quantity,
            'stock_location' => $p->stock_location,
        ])->toArray();
        $this->stock_applied = $workOrder->stock_applied_at !== null;
        $this->showFormModal = true;
    }

    public function openPhotos(int $id): void
    {
        $this->editingId = $id;
        $this->showPhotosModal = true;
    }

    public function saveWorkOrder(): void
    {
        $this->validate();

        $done = 0;
        $total = 0;
        foreach ($this->tasks as $t) {
            if (! filled($t['title'] ?? null)) {
                continue;
            }
            $total++;
            if (! empty($t['is_done'])) {
                $done++;
            }
        }
        if ($total > 0) {
            $this->completion_percent = (int) round(($done / $total) * 100);
        }
        
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'diagnostic_id' => $this->diagnostic_id,
            'mechanic_id' => $this->mechanic_id,
            'transfer_reference' => $this->transfer_reference ?: null,
            'transfer_date' => $this->transfer_date ?: null,
            'work_date' => $this->work_date,
            'work_description' => $this->work_description,
            'parts_used' => $this->parts_used ?: null,
            'parts_removed' => $this->parts_removed ?: null,
            'equipment_used' => $this->equipment_used ?: null,
            'tools_used' => $this->tools_used ?: null,
            'technical_notes' => $this->technical_notes ?: null,
            'problems_found' => $this->problems_found ?: null,
            'solutions_applied' => $this->solutions_applied ?: null,
            'quality_control' => $this->quality_control ?: null,
            'final_checks' => $this->final_checks ?: null,
            'labor_cost' => $this->labor_cost ?: null,
            'parts_cost' => $this->parts_cost ?: null,
            'completion_notes' => $this->completion_notes ?: null,
            'status' => $this->status,
            'completion_percent' => max(0, min(100, (int) $this->completion_percent)),
        ];

        // Calculer le coût total
        if ($this->labor_cost || $this->parts_cost) {
            $data['total_cost'] = (float) ($this->labor_cost ?: 0) + (float) ($this->parts_cost ?: 0);
        }

        // Ajouter les heures si fournies
        if ($this->start_time && $this->work_date) {
            $data['start_time'] = $this->work_date . ' ' . $this->start_time . ':00';
        }
        if ($this->end_time && $this->work_date) {
            $data['end_time'] = $this->work_date . ' ' . $this->end_time . ':00';
        }

        if ($this->editingId) {
            $workOrder = WorkOrder::findOrFail($this->editingId);
            $workOrder->update($data);
            $workOrder->tasks()->delete();
            $workOrder->parts()->delete();
            foreach ($this->tasks as $task) {
                if (! filled($task['title'] ?? null)) {
                    continue;
                }
                $workOrder->tasks()->create([
                    'title' => $task['title'],
                    'estimated_minutes' => $task['estimated_minutes'] !== '' ? (int) $task['estimated_minutes'] : null,
                    'mechanic_id' => $task['mechanic_id'] ?: null,
                    'is_done' => (bool) ($task['is_done'] ?? false),
                ]);
            }
            foreach ($this->parts_lines as $line) {
                if (! filled($line['article_id'] ?? null) || ! filled($line['quantity'] ?? null)) {
                    continue;
                }
                $qty = (int) $line['quantity'];
                $workOrder->parts()->create([
                    'article_id' => (int) $line['article_id'],
                    'quantity' => $qty,
                    'stock_location' => $line['stock_location'] ?? 'main',
                ]);
            }
            $this->dispatch('notify', type: 'success', message: 'Bon de travail mis à jour.');
        } else {
            $data['reference'] = WorkOrder::generateReference();
            $workOrder = WorkOrder::create($data);
            foreach ($this->tasks as $task) {
                if (! filled($task['title'] ?? null)) {
                    continue;
                }
                $workOrder->tasks()->create([
                    'title' => $task['title'],
                    'estimated_minutes' => $task['estimated_minutes'] !== '' ? (int) $task['estimated_minutes'] : null,
                    'mechanic_id' => $task['mechanic_id'] ?: null,
                    'is_done' => (bool) ($task['is_done'] ?? false),
                ]);
            }
            foreach ($this->parts_lines as $line) {
                if (! filled($line['article_id'] ?? null) || ! filled($line['quantity'] ?? null)) {
                    continue;
                }
                $qty = (int) $line['quantity'];
                $workOrder->parts()->create([
                    'article_id' => (int) $line['article_id'],
                    'quantity' => $qty,
                    'stock_location' => $line['stock_location'] ?? 'main',
                ]);
            }
            $this->dispatch('notify', type: 'success', message: 'Bon de travail créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteWorkOrder(): void
    {
        if ($this->editingId) {
            WorkOrder::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Bon de travail supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function validateWorkOrder(int $id): void
    {
        $workOrder = WorkOrder::findOrFail($id);
        $workOrder->update([
            'status' => WorkOrder::STATUS_VALIDATED,
            'completion_percent' => 100,
            'validation_date' => now(),
        ]);
        $this->dispatch('notify', type: 'success', message: 'Bon de travail validé.');
    }

    public function viewWorkOrder(int $id): void
    {
        $this->editingId = $id;
        $workOrder = WorkOrder::findOrFail($id);
        
        // Pré-remplir les champs pour la vue
        $this->vehicle_id = $workOrder->vehicle_id;
        $this->diagnostic_id = $workOrder->diagnostic_id;
        $this->mechanic_id = $workOrder->mechanic_id;
        $this->work_date = $workOrder->work_date->format('Y-m-d');
        $this->transfer_reference = $workOrder->transfer_reference ?? '';
        $this->transfer_date = $workOrder->transfer_date?->format('Y-m-d') ?? '';
        $this->start_time = $workOrder->start_time?->format('H:i') ?? '';
        $this->end_time = $workOrder->end_time?->format('H:i') ?? '';
        $this->work_description = $workOrder->work_description;
        $this->parts_used = $workOrder->parts_used ?? '';
        $this->parts_removed = $workOrder->parts_removed ?? '';
        $this->equipment_used = $workOrder->equipment_used ?? '';
        $this->tools_used = $workOrder->tools_used ?? '';
        $this->technical_notes = $workOrder->technical_notes ?? '';
        $this->problems_found = $workOrder->problems_found ?? '';
        $this->solutions_applied = $workOrder->solutions_applied ?? '';
        $this->quality_control = $workOrder->quality_control ?? '';
        $this->final_checks = $workOrder->final_checks ?? '';
        $this->labor_cost = $workOrder->labor_cost ? number_format($workOrder->labor_cost, 2, '.', '') : '';
        $this->parts_cost = $workOrder->parts_cost ? number_format($workOrder->parts_cost, 2, '.', '') : '';
        $this->completion_notes = $workOrder->completion_notes ?? '';
        $this->status = $workOrder->status;
        $this->completion_percent = (int) ($workOrder->completion_percent ?? 0);
        $this->tasks = $workOrder->tasks->map(fn ($t) => [
            'title' => $t->title,
            'estimated_minutes' => $t->estimated_minutes,
            'mechanic_id' => $t->mechanic_id,
            'is_done' => (bool) $t->is_done,
        ])->toArray();
        
        $this->showViewModal = true;
    }

    public function downloadPDF(int $id): void
    {
        $workOrder = WorkOrder::findOrFail($id);
        
        // Générer le PDF et le télécharger
        $this->dispatch('download-work-order-pdf', id: $id);
    }

    private function resetForm(): void
    {
        $this->vehicle_id = null;
        $this->diagnostic_id = null;
        $this->mechanic_id = null;
        $this->work_date = '';
        $this->transfer_reference = '';
        $this->transfer_date = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->work_description = '';
        $this->parts_used = '';
        $this->parts_removed = '';
        $this->equipment_used = '';
        $this->tools_used = '';
        $this->technical_notes = '';
        $this->problems_found = '';
        $this->solutions_applied = '';
        $this->quality_control = '';
        $this->final_checks = '';
        $this->labor_cost = '';
        $this->parts_cost = '';
        $this->completion_notes = '';
        $this->status = WorkOrder::STATUS_PENDING;
        $this->completion_percent = 0;
        $this->tasks = [['title' => '', 'estimated_minutes' => '', 'mechanic_id' => null, 'is_done' => false]];
        $this->parts_lines = [['article_id' => null, 'quantity' => '1', 'stock_location' => 'main']];
        $this->stock_applied = false;
        $this->resetValidation();
    }

    public function addTaskLine(): void
    {
        $this->tasks[] = ['title' => '', 'estimated_minutes' => '', 'mechanic_id' => null, 'is_done' => false];
    }

    public function removeTaskLine(int $index): void
    {
        if (count($this->tasks) <= 1) {
            return;
        }
        array_splice($this->tasks, $index, 1);
    }

    public function updatedDiagnosticId(): void
    {
        if (! $this->diagnostic_id) {
            return;
        }
        $diag = Diagnostic::with('vehicle')->find($this->diagnostic_id);
        if (! $diag) {
            return;
        }
        $this->vehicle_id = $diag->vehicle_id;
        $this->work_date = $diag->diagnostic_date?->format('Y-m-d') ?? $this->work_date;
        $this->transfer_reference = 'TR-' . $diag->reference;
        $this->transfer_date = $diag->diagnostic_date?->format('Y-m-d') ?? '';
        if ($this->work_description === '') {
            $this->work_description = (string) ($diag->internal_works ?: $diag->external_works ?: '');
        }
    }

    public function addPartLine(): void
    {
        $this->parts_lines[] = ['article_id' => null, 'quantity' => '1', 'stock_location' => 'main'];
    }

    public function removePartLine(int $index): void
    {
        if (count($this->parts_lines) <= 1) return;
        array_splice($this->parts_lines, $index, 1);
    }

    public function applyStockExit(): void
    {
        if (! $this->editingId) return;
        $workOrder = WorkOrder::with('parts')->findOrFail($this->editingId);
        if ($workOrder->stock_applied_at) {
            $this->dispatch('notify', type: 'warning', message: 'Sorties de stock déjà appliquées.');
            return;
        }

        foreach ($workOrder->parts as $p) {
            $stock = \App\Models\Stock::firstOrCreate(
                ['article_id' => $p->article_id, 'location' => $p->stock_location],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            if ($stock->quantity < $p->quantity) {
                $this->dispatch('notify', type: 'error', message: 'Stock insuffisant pour certaines pièces.');
                return;
            }
        }

        foreach ($workOrder->parts as $p) {
            $stock = \App\Models\Stock::firstOrCreate(
                ['article_id' => $p->article_id, 'location' => $p->stock_location],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            $stock->decrement('quantity', $p->quantity);

            \App\Models\StockMovement::create([
                'article_id' => $p->article_id,
                'location' => $p->stock_location,
                'type' => \App\Models\StockMovement::TYPE_EXIT,
                'quantity' => $p->quantity,
                'reference' => $workOrder->reference,
                'reason' => 'Consommation / BT ' . $workOrder->reference,
                'repair_id' => null,
                'user_id' => Auth::id(),
            ]);
        }

        $workOrder->update(['stock_applied_at' => now()]);
        $this->stock_applied = true;
        $this->dispatch('notify', type: 'success', message: 'Sorties de stock appliquées.');
    }

    public function uploadBeforePhoto(): void
    {
        if (! $this->editingId || ! $this->before_photo_file) return;
        $this->validateOnly('before_photo_file');
        $wo = WorkOrder::findOrFail($this->editingId);
        \App\Models\WorkOrderPhoto::storeUpload($wo, $this->before_photo_file, 'before', $this->photo_caption ?: null, $this->photo_taken_at ?: null);
        $this->before_photo_file = null;
        $this->photo_caption = '';
        $this->photo_taken_at = '';
        $this->dispatch('notify', type: 'success', message: 'Photo avant ajoutée.');
    }

    public function uploadAfterPhoto(): void
    {
        if (! $this->editingId || ! $this->after_photo_file) return;
        $this->validateOnly('after_photo_file');
        $wo = WorkOrder::findOrFail($this->editingId);
        \App\Models\WorkOrderPhoto::storeUpload($wo, $this->after_photo_file, 'after', $this->photo_caption ?: null, $this->photo_taken_at ?: null);
        $this->after_photo_file = null;
        $this->photo_caption = '';
        $this->photo_taken_at = '';
        $this->dispatch('notify', type: 'success', message: 'Photo après ajoutée.');
    }

    public function deletePhoto(int $photoId): void
    {
        $photo = \App\Models\WorkOrderPhoto::findOrFail($photoId);

        // Vérifier que la photo appartient bien au bon de travail en cours d'édition
        if ($this->editingId && $photo->work_order_id === $this->editingId) {
            // Supprimer le fichier du stockage
            if (\Storage::disk('public')->exists($photo->file_path)) {
                \Storage::disk('public')->delete($photo->file_path);
            }

            // Supprimer de la base de données
            $photo->delete();

            $this->dispatch('notify', type: 'success', message: 'Photo supprimée.');
        } else {
            $this->dispatch('notify', type: 'error', message: 'Erreur lors de la suppression de la photo.');
        }
    }

    public function render(): View
    {
        $query = WorkOrder::query()->with(['vehicle:id,registration', 'mechanic:id,first_name,last_name', 'diagnostic:id,reference', 'tasks', 'parts', 'photos']);
        
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->whereHas('vehicle', fn ($q2) => $q2->where('registration', 'like', '%' . $this->search . '%'))
                    ->orWhere('reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('mechanic', fn ($q2) => $q2->where('first_name', 'like', '%' . $this->search . '%'))
                    ->orWhere('work_description', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }
        
        if ($this->date_filter !== '') {
            $query->whereDate('work_date', $this->date_filter);
        }

        $workOrders = $query->orderByDesc('work_date')->paginate(12);
        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);
        $mechanics = Mechanic::where('is_active', true)->orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $diagnostics = Diagnostic::orderByDesc('diagnostic_date')->get(['id', 'reference', 'vehicle_id']);
        $articles = \App\Models\Article::where('is_active', true)->orderBy('name')->get(['id','name','reference']);

        $statusStats = [
            'pending' => WorkOrder::where('status', WorkOrder::STATUS_PENDING)->count(),
            'in_progress' => WorkOrder::where('status', WorkOrder::STATUS_IN_PROGRESS)->count(),
            'completed' => WorkOrder::where('status', WorkOrder::STATUS_COMPLETED)->count(),
            'validated' => WorkOrder::where('status', WorkOrder::STATUS_VALIDATED)->count(),
            'avg_progress' => (int) round((float) WorkOrder::avg('completion_percent')),
        ];

        return view('livewire.portal.work-orders.index', [
            'workOrders' => $workOrders,
            'vehicles' => $vehicles,
            'mechanics' => $mechanics,
            'diagnostics' => $diagnostics,
            'statusStats' => $statusStats,
            'articles' => $articles,
        ])->layout('layouts.app', ['title' => 'Bons de travail']);
    }
}
