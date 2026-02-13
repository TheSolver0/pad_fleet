<?php

namespace App\Livewire\Portal\Stock;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Articles extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $category_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $reference = '';
    public string $name = '';
    public string $description = '';
    public ?int $article_category_id = null;
    public string $brand = '';
    public string $model = '';
    public string $unit = 'unité';
    public string $purchase_price = '';
    public string $tire_size = '';
    public array $compatible_vehicle_categories = [];
    public $photo_file = null;
    public bool $is_active = true;

    protected $queryString = ['search' => ['except' => ''], 'category_filter' => ['except' => '']];

    protected function rules(): array
    {
        $unique = $this->editingId
            ? 'required|string|max:50|unique:articles,reference,' . $this->editingId
            : 'required|string|max:50|unique:articles,reference';
            
        return [
            'reference' => $unique,
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'article_category_id' => 'required|exists:article_categories,id',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'tire_size' => 'nullable|string|max:50',
            'compatible_vehicle_categories' => 'nullable|array',
            'compatible_vehicle_categories.*' => 'in:leger,utilitaire,camionnette,4x4,lourd,bus,moto,autre',
            'photo_file' => 'nullable|image|max:2048',
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
        $article = Article::findOrFail($id);
        $this->editingId = $article->id;
        $this->reference = $article->reference;
        $this->name = $article->name;
        $this->description = $article->description ?? '';
        $this->article_category_id = $article->article_category_id;
        $this->brand = $article->brand ?? '';
        $this->model = $article->model ?? '';
        $this->unit = $article->unit;
        $this->purchase_price = $article->purchase_price ? number_format($article->purchase_price, 2, ',', ' ') : '';
        $this->tire_size = $article->tire_size ?? '';
        $this->compatible_vehicle_categories = $article->compatible_vehicle_categories ?? [];
        $this->is_active = $article->is_active;
        $this->showFormModal = true;
    }

    public function saveArticle(): void
    {
        $this->purchase_price = str_replace([' ', ','], ['', '.'], $this->purchase_price);
        
        $this->validate();
        
        $data = [
            'reference' => $this->reference,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'article_category_id' => $this->article_category_id,
            'brand' => $this->brand ?: null,
            'model' => $this->model ?: null,
            'unit' => $this->unit,
            'purchase_price' => $this->purchase_price ?: null,
            'selling_price' => $this->selling_price ?: null,
            'min_stock_level' => (int) $this->min_stock_level,
            'max_stock_level' => $this->max_stock_level ? (int) $this->max_stock_level : null,
            'tire_size' => $this->tire_size ?: null,
            'compatible_vehicle_categories' => $this->compatible_vehicle_categories,
            'is_active' => $this->is_active,
        ];

        if ($this->photo_file) {
            $data['photo_path'] = $this->photo_file->store('articles', 'public');
        }

        if ($this->editingId) {
            Article::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Article mis à jour.');
        } else {
            Article::create($data);
            $this->dispatch('notify', type: 'success', message: 'Article créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteArticle(): void
    {
        if ($this->editingId) {
            Article::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Article supprimé.');
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
        $this->reference = '';
        $this->name = '';
        $this->description = '';
        $this->article_category_id = null;
        $this->brand = '';
        $this->model = '';
        $this->unit = 'unité';
        $this->purchase_price = '';
        $this->tire_size = '';
        $this->compatible_vehicle_categories = [];
        $this->photo_file = null;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Article::with(['category', 'stocks']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('brand', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category_filter !== '') {
            $query->where('article_category_id', $this->category_filter);
        }

        $articles = $query->orderBy('name')->paginate(20);
        $categories = ArticleCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.stock.articles', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }
}
