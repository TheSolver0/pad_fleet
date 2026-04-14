<?php

namespace App\Livewire\Portal\Drivers;

use App\Models\Driver;
use App\Models\DriverLeave;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Leaves extends Component
{
    use WithPagination, WithFileUploads;

    public string $search        = '';
    public string $status_filter = '';
    public bool   $showFormModal   = false;
    public bool   $showDeleteModal = false;
    public ?int   $editingId       = null;

    public ?int   $driver_id          = null;
    public string $type               = DriverLeave::TYPE_CONGE_ANNUEL;
    public string $start_date         = '';
    public string $end_date           = '';
    public string $reason             = '';
    public string $notes              = '';
    public ?int   $replacement_driver_id = null;
    public $justificatif              = null;

    protected $paginationTheme = 'bootstrap';

    protected function rules(): array
    {
        return [
            'driver_id'  => 'required|exists:drivers,id',
            'type'       => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:500',
            'notes'      => 'nullable|string',
            'replacement_driver_id' => 'nullable|exists:drivers,id',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
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
        $leave = DriverLeave::findOrFail($id);
        $this->editingId     = $id;
        $this->driver_id     = $leave->driver_id;
        $this->type          = $leave->type;
        $this->start_date    = $leave->start_date->format('Y-m-d');
        $this->end_date      = $leave->end_date->format('Y-m-d');
        $this->reason        = $leave->reason ?? '';
        $this->notes         = $leave->notes ?? '';
        $this->replacement_driver_id = $leave->replacement_driver_id;
        $this->showFormModal = true;
    }

    public function saveLeave(): void
    {
        $this->validate();

        $nbJours = max(1, (int) \Carbon\Carbon::parse($this->start_date)->diffInDays(\Carbon\Carbon::parse($this->end_date)) + 1);

        $data = [
            'driver_id'   => $this->driver_id,
            'type'        => $this->type,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date,
            'nb_jours'    => $nbJours,
            'reason'      => $this->reason ?: null,
            'notes'       => $this->notes ?: null,
            'replacement_driver_id' => $this->replacement_driver_id ?: null,
        ];

        if ($this->justificatif) {
            $path = $this->justificatif->store('drivers/leaves', 'public');
            $data['document_path'] = $path;
        }

        if ($this->editingId) {
            DriverLeave::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Congé mis à jour.');
        } else {
            DriverLeave::create($data);
            $this->dispatch('notify', type: 'success', message: 'Congé enregistré.');
        }

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $leave = DriverLeave::findOrFail($id);
        $leave->update([
            'status'      => DriverLeave::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        // Mettre le chauffeur comme non disponible si le congé est actif maintenant
        if ($leave->isActive()) {
            $leave->driver()->update(['is_available' => false]);
        }
        $this->dispatch('notify', type: 'success', message: 'Congé approuvé.');
    }

    public function reject(int $id): void
    {
        DriverLeave::findOrFail($id)->update(['status' => DriverLeave::STATUS_REJECTED]);
        $this->dispatch('notify', type: 'success', message: 'Congé refusé.');
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLeave(): void
    {
        if ($this->editingId) {
            DriverLeave::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Congé supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->driver_id     = null;
        $this->type          = DriverLeave::TYPE_CONGE_ANNUEL;
        $this->start_date    = '';
        $this->end_date      = '';
        $this->reason        = '';
        $this->notes         = '';
        $this->replacement_driver_id = null;
        $this->justificatif  = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = DriverLeave::query()->with(['driver', 'replacementDriver:id,first_name,last_name', 'approvedBy:id,name']);

        if ($this->search !== '') {
            $query->whereHas('driver', fn ($q) =>
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
            );
        }
        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }

        $leaves  = $query->orderByDesc('start_date')->paginate(15);
        $drivers = Driver::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $types   = DriverLeave::typeOptions();

        return view('livewire.portal.drivers.leaves', [
            'leaves'  => $leaves,
            'drivers' => $drivers,
            'types'   => $types,
        ])->layout('layouts.app', ['title' => 'Congés des chauffeurs']);
    }
}
