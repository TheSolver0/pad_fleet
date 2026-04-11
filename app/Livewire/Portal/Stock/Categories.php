<?php

namespace App\Livewire\Portal\Stock;

use App\Models\ArticleCategory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $code = '';
    public string $description = '';
    public bool $is_active = true;

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        $unique = $this->editingId
            ? 'required|string|max:100|unique:article_categories,name,' . $this->editingId
            : 'required|string|max:100|unique:article_categories,name';
            
        return [
            'name' => $unique,
            'code' => 'nullable|string|max:50|unique:article_categories,code,' . ($this->editingId ?? 'NULL'),
            'description' => 'nullable|string',
            'is_active' => 'boolean',
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
        $category = ArticleCategory::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->code = $category->code ?? '';
        $this->description = $category->description ?? '';
        $this->is_active = $category->is_active;
        $this->showFormModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate();
        
        $data = [
            'name' => $this->name,
            'code' => $this->code ?: null,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            ArticleCategory::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Catégorie mise à jour.');
        } else {
            ArticleCategory::create($data);
            $this->dispatch('notify', type: 'success', message: 'Catégorie créée.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCategory(): void
    {
        if ($this->editingId) {
            $category = ArticleCategory::findOrFail($this->editingId);
            
            // Vérifier si des articles sont associés
            if ($category->articles()->count() > 0) {
                $this->dispatch('notify', type: 'error', message: 'Impossible de supprimer cette catégorie car des articles y sont associés.');
                $this->showDeleteModal = false;
                $this->editingId = null;
                return;
            }
            
            $category->delete();
            $this->dispatch('notify', type: 'success', message: 'Catégorie supprimée.');
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
        $this->description = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = ArticleCategory::withCount('articles');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        $categories = $query->orderBy('name')->paginate(20);

        return view('livewire.portal.stock.categories', [
            'categories' => $categories,
        ]);
    }
}
