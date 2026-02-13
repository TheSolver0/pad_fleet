<?php

namespace App\Livewire\Portal\Stock;

use App\Models\Article;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Entries extends Component
{
    use WithPagination;

    public string $search = '';
    public string $entry_type = 'direct';
    public string $location = 'main';
    public bool $showFormModal = false;
    public bool $showSupplierModal = false;

    // Champs pour entrée directe
    public ?int $article_id = null;
    public string $quantity = '';
    public string $reference = '';
    public string $notes = '';
    public ?int $supplier_id = null;
    public string $supplier_name = '';
    public string $supplier_phone = '';
    public string $supplier_email = '';
    public bool $create_supplier = false;
    
    // Champs pour créer un nouvel article
    public bool $create_article = false;
    public string $new_article_name = '';
    public string $new_article_reference = '';
    public ?int $new_article_category_id = null;
    public string $new_article_brand = '';
    public string $new_article_unit = 'unité';

    // Champs pour bon de commande
    public ?int $purchase_order_id = null;

    protected $queryString = ['search' => ['except' => ''], 'entry_type' => ['except' => 'direct']];

    protected function rules(): array
    {
        $rules = [
            'location' => 'required|in:main,garage',
            'notes' => 'nullable|string',
        ];

        if ($this->entry_type === 'direct') {
            $rules['article_id'] = 'required_without:create_article|exists:articles,id';
            $rules['quantity'] = 'required|integer|min:1';
            $rules['reference'] = 'required|string|max:100';
            $rules['supplier_id'] = 'nullable|exists:suppliers,id';
            
            if ($this->create_supplier) {
                $rules['supplier_name'] = 'required|string|max:200';
                $rules['supplier_phone'] = 'nullable|string|max:30';
                $rules['supplier_email'] = 'nullable|email|max:150';
            }
            
            if ($this->create_article) {
                $rules['new_article_name'] = 'required|string|max:200';
                $rules['new_article_reference'] = 'nullable|string|max:100';
                $rules['new_article_category_id'] = 'required|exists:article_categories,id';
                $rules['new_article_brand'] = 'nullable|string|max:100';
                $rules['new_article_unit'] = 'required|string|max:50';
            }
        } else {
            $rules['purchase_order_id'] = 'required|exists:purchase_orders,id';
        }

        return $rules;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function saveEntry(): void
    {
        $this->validate();
        
        if ($this->entry_type === 'direct') {
            $this->saveDirectEntry();
        } else {
            $this->savePurchaseOrderEntry();
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    private function saveDirectEntry(): void
    {
        // Créer le fournisseur si nécessaire
        if ($this->create_supplier && $this->supplier_name) {
            $supplier = Supplier::create([
                'name' => $this->supplier_name,
                'phone' => $this->supplier_phone ?: null,
                'email' => $this->supplier_email ?: null,
                'is_active' => true,
            ]);
            $this->supplier_id = $supplier->id;
        }

        // Créer l'article si nécessaire
        if ($this->create_article && $this->new_article_name) {
            $article = Article::create([
                'reference' => $this->new_article_reference ?: 'AUTO-' . time(),
                'name' => $this->new_article_name,
                'article_category_id' => $this->new_article_category_id,
                'brand' => $this->new_article_brand,
                'unit' => $this->new_article_unit,
                'is_active' => true,
            ]);
            $this->article_id = $article->id;
        }

        // Créer le mouvement de stock
        StockMovement::create([
            'article_id' => $this->article_id,
            'location' => $this->location,
            'type' => StockMovement::TYPE_ENTRY,
            'quantity' => (int) $this->quantity,
            'reference' => $this->reference,
            'reason' => $this->notes ?: 'Achat direct',
            'supplier_id' => $this->supplier_id,
            'user_id' => Auth::id(),
        ]);

        // Mettre à jour le stock
        $stock = Stock::firstOrCreate(
            ['article_id' => $this->article_id, 'location' => $this->location],
            ['quantity' => 0, 'reserved_quantity' => 0]
        );
        $stock->increment('quantity', (int) $this->quantity);

        $this->dispatch('notify', type: 'success', message: 'Entrée de stock enregistrée.');
    }

    private function savePurchaseOrderEntry(): void
    {
        $purchaseOrder = PurchaseOrder::findOrFail($this->purchase_order_id);
        
        // Pour simplifier, on enregistre la réception complète
        foreach ($purchaseOrder->items as $item) {
            if ($item->remaining_quantity > 0) {
                // Créer le mouvement de stock
                StockMovement::create([
                    'article_id' => $item->article_id,
                    'location' => $this->location,
                    'type' => StockMovement::TYPE_ENTRY,
                    'quantity' => $item->remaining_quantity,
                    'reference' => $purchaseOrder->reference,
                    'reason' => 'Réception bon de commande',
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'purchase_order_id' => $purchaseOrder->id,
                    'user_id' => Auth::id(),
                ]);

                // Mettre à jour le stock
                $stock = Stock::firstOrCreate(
                    ['article_id' => $item->article_id, 'location' => $this->location],
                    ['quantity' => 0, 'reserved_quantity' => 0]
                );
                $stock->increment('quantity', $item->remaining_quantity);

                // Marquer comme complètement reçu
                $item->update(['quantity_received' => $item->quantity]);
            }
        }

        // Mettre à jour le statut de la commande
        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_RECEIVED]);

        $this->dispatch('notify', type: 'success', message: 'Réception du bon de commande enregistrée.');
    }

    public function updatedEntryType(): void
    {
        $this->reset(['purchase_order_id', 'article_id', 'quantity', 'reference']);
    }

    public function updatedCreateSupplier(): void
    {
        if (!$this->create_supplier) {
            $this->reset(['supplier_name', 'supplier_phone', 'supplier_email']);
        }
    }

    public function updatedCreateArticle(): void
    {
        if (!$this->create_article) {
            $this->reset(['new_article_name', 'new_article_reference', 'new_article_category_id', 'new_article_brand', 'new_article_unit']);
            $this->article_id = null;
        } else {
            $this->article_id = null; // Désélectionner l'article existant
        }
    }

    private function resetForm(): void
    {
        $this->article_id = null;
        $this->quantity = '';
        $this->reference = '';
        $this->notes = '';
        $this->supplier_id = null;
        $this->supplier_name = '';
        $this->supplier_phone = '';
        $this->supplier_email = '';
        $this->create_supplier = false;
        $this->create_article = false;
        $this->new_article_name = '';
        $this->new_article_reference = '';
        $this->new_article_category_id = null;
        $this->new_article_brand = '';
        $this->new_article_unit = 'unité';
        $this->purchase_order_id = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = StockMovement::with(['article.category', 'supplier', 'purchaseOrder', 'user'])
            ->where('type', StockMovement::TYPE_ENTRY)
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('article', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('reference', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('supplier', function ($q3) {
                        $q3->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $entries = $query->paginate(20);
        
        $articles = Article::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'reference', 'brand']);
            
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        $purchaseOrders = PurchaseOrder::where('status', PurchaseOrder::STATUS_APPROVED)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'reference', 'supplier_id']);

        $categories = \App\Models\ArticleCategory::orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.portal.stock.entries', [
            'entries' => $entries,
            'articles' => $articles,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'purchaseOrders' => $purchaseOrders,
        ]);
    }
}
