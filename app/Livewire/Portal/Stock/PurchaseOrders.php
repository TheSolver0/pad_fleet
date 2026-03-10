<?php

namespace App\Livewire\Portal\Stock;

use App\Models\Article;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrders extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $supplier_id = null;
    public string $order_date = '';
    public string $expected_delivery_date = '';
    public string $notes = '';
    public array $lines = []; // [ ['article_id' => x, 'quantity' => 1, 'unit_price' => ''], ... ]

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => '']];

    public function mount(): void
    {
        if ($this->order_date === '') {
            $this->order_date = now()->format('Y-m-d');
        }
    }

    protected function rules(): array
    {
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
        foreach ($this->lines as $i => $line) {
            $rules["lines.{$i}.article_id"] = 'required|exists:articles,id';
            $rules["lines.{$i}.quantity"] = 'required|integer|min:1';
            $rules["lines.{$i}.unit_price"] = 'nullable|numeric|min:0';
        }
        return $rules;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->order_date = now()->format('Y-m-d');
        $this->lines = [['article_id' => null, 'quantity' => '1', 'unit_price' => '']];
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $po = PurchaseOrder::with('items.article')->findOrFail($id);
        $this->editingId = $po->id;
        $this->supplier_id = $po->supplier_id;
        $this->order_date = $po->order_date->format('Y-m-d');
        $this->expected_delivery_date = $po->expected_delivery_date?->format('Y-m-d') ?? '';
        $this->notes = $po->notes ?? '';
        $this->lines = $po->items->map(fn ($item) => [
            'article_id' => $item->article_id,
            'quantity' => (string) $item->quantity,
            'unit_price' => $item->unit_price ? number_format($item->unit_price, 2, '.', '') : '',
        ])->toArray();
        $this->showFormModal = true;
    }

    public function addLine(): void
    {
        $this->lines[] = ['article_id' => null, 'quantity' => '1', 'unit_price' => ''];
    }

    public function removeLine(int $index): void
    {
        array_splice($this->lines, $index, 1);
    }

    public function saveOrder(): void
    {
        $this->validate();
        $totalAmount = 0;
        foreach ($this->lines as $line) {
            $qty = (int) $line['quantity'];
            $unit = (float) ($line['unit_price'] ?? 0);
            $totalAmount += $qty * $unit;
        }

        $data = [
            'supplier_id' => $this->supplier_id,
            'order_date' => $this->order_date,
            'expected_delivery_date' => $this->expected_delivery_date ?: null,
            'notes' => $this->notes ?: null,
            'total_amount' => $totalAmount,
            'tax_amount' => 0,
            'status' => PurchaseOrder::STATUS_PENDING,
            'user_id' => auth()->id(),
        ];

        if ($this->editingId) {
            $po = PurchaseOrder::findOrFail($this->editingId);
            if ($po->status !== PurchaseOrder::STATUS_PENDING) {
                $this->dispatch('notify', type: 'error', message: 'Seuls les bons en attente peuvent être modifiés.');
                return;
            }
            $po->update($data);
            $po->items()->delete();
            $poId = $po->id;
        } else {
            $data['reference'] = 'BC-' . str_pad((string) ((PurchaseOrder::max('id') ?? 0) + 1), 5, '0', STR_PAD_LEFT) . '-' . date('Ymd');
            $po = PurchaseOrder::create($data);
            $poId = $po->id;
        }

        foreach ($this->lines as $line) {
            $qty = (int) $line['quantity'];
            $unit = (float) ($line['unit_price'] ?? 0);
            PurchaseOrderItem::create([
                'purchase_order_id' => $poId,
                'article_id' => $line['article_id'],
                'quantity' => $qty,
                'unit_price' => $unit,
                'total_price' => $qty * $unit,
            ]);
        }

        if ($this->editingId) {
            $this->dispatch('notify', type: 'success', message: 'Bon de commande mis à jour.');
        } else {
            $this->dispatch('notify', type: 'success', message: 'Bon de commande créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function approve(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        if ($po->status !== PurchaseOrder::STATUS_PENDING) {
            $this->dispatch('notify', type: 'error', message: 'Ce bon n\'est pas en attente.');
            return;
        }
        $po->update(['status' => PurchaseOrder::STATUS_APPROVED]);
        $this->dispatch('notify', type: 'success', message: 'Bon de commande approuvé. Vous pouvez le réceptionner dans Entrées de stock.');
    }

    public function cancelOrder(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        if ($po->status === PurchaseOrder::STATUS_RECEIVED) {
            $this->dispatch('notify', type: 'error', message: 'Impossible d\'annuler un bon déjà reçu.');
            return;
        }
        $po->update(['status' => PurchaseOrder::STATUS_CANCELLED]);
        $this->dispatch('notify', type: 'success', message: 'Bon de commande annulé.');
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->supplier_id = null;
        $this->order_date = now()->format('Y-m-d');
        $this->expected_delivery_date = '';
        $this->notes = '';
        $this->lines = [];
        $this->editingId = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = PurchaseOrder::with(['supplier:id,name', 'user:id,name']);
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', '%' . $this->search . '%'));
            });
        }
        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }
        $orders = $query->orderByDesc('created_at')->paginate(15);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $articles = Article::where('is_active', true)->orderBy('name')->get(['id', 'name', 'reference', 'purchase_price']);

        return view('livewire.portal.stock.purchase-orders', [
            'orders' => $orders,
            'suppliers' => $suppliers,
            'articles' => $articles,
        ])->layout('layouts.app', ['title' => 'Bons de commande']);
    }
}
