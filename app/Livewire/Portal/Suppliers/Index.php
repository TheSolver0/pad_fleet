<?php

namespace App\Livewire\Portal\Suppliers;

use App\Models\Supplier;
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

    public string $name = '';
    public string $code = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $contact_person = '';
    public string $contact_phone = '';
    public bool $is_active = true;
    public string $notes = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules(): array
    {
        $unique = $this->editingId
            ? 'nullable|string|max:50|unique:suppliers,code,' . $this->editingId
            : 'nullable|string|max:50|unique:suppliers,code';
            
        return [
            'name' => 'required|string|max:200',
            'code' => $unique,
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:150',
            'contact_phone' => 'nullable|string|max:30',
            'is_active' => 'boolean',
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
        $supplier = Supplier::findOrFail($id);
        $this->editingId = $supplier->id;
        $this->name = $supplier->name;
        $this->code = $supplier->code ?? '';
        $this->phone = $supplier->phone ?? '';
        $this->email = $supplier->email ?? '';
        $this->address = $supplier->address ?? '';
        $this->contact_person = $supplier->contact_person ?? '';
        $this->contact_phone = $supplier->contact_phone ?? '';
        $this->is_active = $supplier->is_active;
        $this->notes = $supplier->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveSupplier(): void
    {
        $this->validate();
        
        $data = [
            'name' => $this->name,
            'code' => $this->code ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'contact_person' => $this->contact_person ?: null,
            'contact_phone' => $this->contact_phone ?: null,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Supplier::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Fournisseur mis à jour.');
        } else {
            Supplier::create($data);
            $this->dispatch('notify', type: 'success', message: 'Fournisseur créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteSupplier(): void
    {
        if ($this->editingId) {
            Supplier::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Fournisseur supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->code = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->contact_person = '';
        $this->contact_phone = '';
        $this->is_active = true;
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Supplier::query()
            ->withAvg('evaluations', 'quality_score')
            ->withAvg('evaluations', 'delivery_score')
            ->withAvg('evaluations', 'reputation_score')
            ->withCount('evaluations')
            ->withCount('purchaseOrders')
            ->withSum('purchaseOrders', 'total_amount');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $suppliers = $query->orderBy('name')->paginate(20);
        $topSuppliers = Supplier::query()
            ->withCount('purchaseOrders')
            ->withSum('purchaseOrders', 'total_amount')
            ->orderByDesc('purchase_orders_count')
            ->limit(5)
            ->get(['id', 'name']);

        return view('livewire.portal.suppliers.index', [
            'suppliers' => $suppliers,
            'topSuppliers' => $topSuppliers,
        ]);
    }
}
