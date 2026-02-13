<?php

namespace App\Livewire\Portal\WorkOrders;

use App\Models\WorkOrder;
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

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => ''], 'date_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'diagnostic_id' => 'nullable|exists:diagnostics,id',
            'mechanic_id' => 'required|exists:mechanics,id',
            'work_date' => 'required|date',
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
        $this->showFormModal = true;
    }

    public function saveWorkOrder(): void
    {
        $this->validate();
        
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'diagnostic_id' => $this->diagnostic_id,
            'mechanic_id' => $this->mechanic_id,
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
            $this->dispatch('notify', type: 'success', message: 'Bon de travail mis à jour.');
        } else {
            $data['reference'] = WorkOrder::generateReference();
            WorkOrder::create($data);
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
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = WorkOrder::query()->with(['vehicle:id,registration', 'mechanic:id,first_name,last_name', 'diagnostic:id,reference']);
        
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

        return view('livewire.portal.work-orders.index', [
            'workOrders' => $workOrders,
            'vehicles' => $vehicles,
            'mechanics' => $mechanics,
            'diagnostics' => $diagnostics,
        ])->layout('layouts.app', ['title' => 'Bons de travail']);
    }
}
