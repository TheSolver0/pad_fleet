<?php

namespace App\Livewire\Portal\Assurances;

use App\Models\InsuranceContractGlobal;
use App\Models\InsuranceContractGlobalDocument;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public bool $showFormModal = false;
    public bool $showDocModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $docContractId = null;

    public string $name = '';
    public string $insurer = '';
    public string $lot_description = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $optional_prime = '';
    public string $notes = '';

    public $contract_file = null;
    public string $contract_original_name = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'insurer' => 'required|string|max:255',
            'lot_description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'optional_prime' => 'nullable|numeric|min:0',
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
        $c = InsuranceContractGlobal::findOrFail($id);
        $this->editingId = $c->id;
        $this->name = $c->name;
        $this->insurer = $c->insurer;
        $this->lot_description = $c->lot_description ?? '';
        $this->start_date = $c->start_date->format('Y-m-d');
        $this->end_date = $c->end_date->format('Y-m-d');
        $this->optional_prime = $c->optional_prime ? format_money($c->optional_prime, 2) : '';
        $this->notes = $c->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveContract(): void
    {
        $this->optional_prime = parse_french_number($this->optional_prime) ?? $this->optional_prime;
        $this->validate();
        $data = [
            'name' => $this->name,
            'insurer' => $this->insurer,
            'lot_description' => $this->lot_description ?: null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'optional_prime' => $this->optional_prime ?: null,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            InsuranceContractGlobal::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Marché mis à jour.');
        } else {
            InsuranceContractGlobal::create($data);
            $this->dispatch('notify', type: 'success', message: 'Marché créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function openDocModal(int $id): void
    {
        $this->docContractId = $id;
        $this->contract_file = null;
        $this->showDocModal = true;
    }

    public function uploadContractDoc(): void
    {
        $this->validate(['contract_file' => 'required|file|max:15360']);
        $contract = InsuranceContractGlobal::findOrFail($this->docContractId);
        InsuranceContractGlobalDocument::storeUpload($contract, $this->contract_file);
        $this->dispatch('notify', type: 'success', message: 'Contrat ajouté.');
        $this->contract_file = null;
    }

    public function deleteDoc(int $id): void
    {
        InsuranceContractGlobalDocument::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Document supprimé.');
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteContract(): void
    {
        if ($this->editingId) {
            InsuranceContractGlobal::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Marché supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->insurer = '';
        $this->lot_description = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->optional_prime = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = InsuranceContractGlobal::query();
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('insurer', 'like', '%' . $this->search . '%');
            });
        }
        $contracts = $query->orderBy('end_date', 'desc')->paginate(12);
        $docContract = $this->docContractId ? InsuranceContractGlobal::with('documents')->find($this->docContractId) : null;

        return view('livewire.portal.assurances.index', [
            'contracts' => $contracts,
            'docContract' => $docContract,
        ])->layout('layouts.app', ['title' => 'Assurances globales']);
    }
}
