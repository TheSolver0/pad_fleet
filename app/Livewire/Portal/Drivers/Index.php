<?php

namespace App\Livewire\Portal\Drivers;

use App\Models\Driver;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $availability_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $matricule = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $email = '';
    public string $license_number = '';
    public string $license_category = '';
    public string $license_expiry = '';
    public ?int $service_id = null;
    public bool $is_available = true;
    public string $notes = '';

    protected $queryString = ['search' => ['except' => ''], 'availability_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'matricule' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'license_number' => 'nullable|string|max:100',
            'license_category' => 'nullable|string|max:20',
            'license_expiry' => 'nullable|date',
            'service_id' => 'nullable|exists:services,id',
            'is_available' => 'boolean',
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
        $d = Driver::findOrFail($id);
        $this->editingId = $d->id;
        $this->matricule = $d->matricule ?? '';
        $this->first_name = $d->first_name;
        $this->last_name = $d->last_name;
        $this->phone = $d->phone ?? '';
        $this->email = $d->email ?? '';
        $this->license_number = $d->license_number ?? '';
        $this->license_category = $d->license_category ?? '';
        $this->license_expiry = $d->license_expiry?->format('Y-m-d') ?? '';
        $this->service_id = $d->service_id;
        $this->is_available = $d->is_available;
        $this->notes = $d->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveDriver(): void
    {
        $this->validate();
        $data = [
            'matricule' => $this->matricule ?: null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'license_number' => $this->license_number ?: null,
            'license_category' => $this->license_category ?: null,
            'license_expiry' => $this->license_expiry ?: null,
            'service_id' => $this->service_id,
            'is_available' => $this->is_available,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            Driver::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Chauffeur mis à jour.');
        } else {
            Driver::create($data);
            $this->dispatch('notify', type: 'success', message: 'Chauffeur créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDriver(): void
    {
        if ($this->editingId) {
            Driver::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Chauffeur supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->matricule = '';
        $this->first_name = '';
        $this->last_name = '';
        $this->phone = '';
        $this->email = '';
        $this->license_number = '';
        $this->license_category = '';
        $this->license_expiry = '';
        $this->service_id = null;
        $this->is_available = true;
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Driver::query()->with('service:id,name');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->availability_filter === '1') {
            $query->where('is_available', true);
        } elseif ($this->availability_filter === '0') {
            $query->where('is_available', false);
        }
        $drivers = $query->orderBy('last_name')->paginate(12);
        $services = Service::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.drivers.index', [
            'drivers' => $drivers,
            'services' => $services,
        ])->layout('layouts.app', ['title' => 'Gestion des chauffeurs']);
    }
}
