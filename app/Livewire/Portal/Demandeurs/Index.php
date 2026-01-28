<?php

namespace App\Livewire\Portal\Demandeurs;

use App\Models\Demandeur;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $matricule = '';
    public string $name = '';
    public ?int $service_id = null;
    public string $contact_phone = '';
    public string $contact_email = '';
    public string $notes = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'matricule' => 'nullable|string|max:50',
            'service_id' => 'nullable|exists:services,id',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:100',
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
        $d = Demandeur::findOrFail($id);
        $this->editingId = $d->id;
        $this->matricule = $d->matricule ?? '';
        $this->name = $d->name;
        $this->service_id = $d->service_id;
        $this->contact_phone = $d->contact_phone ?? '';
        $this->contact_email = $d->contact_email ?? '';
        $this->notes = $d->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveDemandeur(): void
    {
        $this->validate();
        $data = [
            'matricule' => $this->matricule ?: null,
            'name' => $this->name,
            'service_id' => $this->service_id,
            'contact_phone' => $this->contact_phone ?: null,
            'contact_email' => $this->contact_email ?: null,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            Demandeur::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Demandeur mis à jour.');
        } else {
            Demandeur::create($data);
            $this->dispatch('notify', type: 'success', message: 'Demandeur créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDemandeur(): void
    {
        if ($this->editingId) {
            Demandeur::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Demandeur supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->matricule = '';
        $this->name = '';
        $this->service_id = null;
        $this->contact_phone = '';
        $this->contact_email = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Demandeur::query()->with('service:id,name');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }
        $demandeurs = $query->orderBy('name')->paginate(12);
        $services = Service::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.demandeurs.index', [
            'demandeurs' => $demandeurs,
            'services' => $services,
        ])->layout('layouts.app', ['title' => 'Demandeurs']);
    }
}
