<?php

namespace App\Livewire\Portal\Repairs;

use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\UsedMaterial;
use App\Models\Article;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StockUsage extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showFormModal = false;
    public ?int $repair_id = null;
    public array $parts = [];
    public array $used_materials = [];
    public string $notes = '';

    protected $queryString = ['search' => ['except' => '']];

    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        $rules = [
            'repair_id' => 'required|exists:repairs,id',
            'notes' => 'nullable|string',
        ];

        // Validation des pièces utilisées
        foreach ($this->parts as $index => $part) {
            $rules["parts.{$index}.article_id"] = 'required|exists:articles,id';
            $rules["parts.{$index}.quantity_used"] = 'required|integer|min:1';
            $rules["parts.{$index}.unit_price"] = 'nullable|numeric|min:0';
            $rules["parts.{$index}.stock_location"] = 'required|in:main,garage';
        }

        // Validation du matériel usé (doit être égal aux pièces utilisées)
        foreach ($this->used_materials as $index => $material) {
            $rules["used_materials.{$index}.article_id"] = 'required|exists:articles,id';
            $rules["used_materials.{$index}.quantity"] = 'required|integer|min:1';
            $rules["used_materials.{$index}.condition"] = 'required|string|max:50';
            $rules["used_materials.{$index}.disposition"] = 'required|string|max:50';
        }

        return $rules;
    }

    public function mount(?int $repairId = null): void
    {
        if ($repairId) {
            $this->repair_id = $repairId;
            $this->loadExistingData();
        }
    }

    public function addPart(): void
    {
        $this->parts[] = [
            'article_id' => null,
            'quantity_used' => 1,
            'unit_price' => '',
            'stock_location' => 'garage',
            'notes' => '',
        ];
    }

    public function removePart(int $index): void
    {
        unset($this->parts[$index]);
        $this->parts = array_values($this->parts);
    }

    public function addUsedMaterial(): void
    {
        $this->used_materials[] = [
            'article_id' => null,
            'quantity' => 1,
            'condition' => 'usé',
            'disposition' => 'stocké',
            'notes' => '',
        ];
    }

    public function removeUsedMaterial(int $index): void
    {
        unset($this->used_materials[$index]);
        $this->used_materials = array_values($this->used_materials);
    }

    public function saveStockUsage(): void
    {
        $this->validate();
        
        $repair = Repair::findOrFail($this->repair_id);

        // Vérifier que la quantité de matériel usé = quantité de pièces changées
        $totalPartsUsed = collect($this->parts)->sum('quantity_used');
        $totalMaterialsUsed = collect($this->used_materials)->sum('quantity');
        
        if ($totalPartsUsed !== $totalMaterialsUsed) {
            $this->dispatch('notify', type: 'error', message: 'La quantité de matériel usé doit être égale à la quantité de pièces changées.');
            return;
        }

        // Enregistrer les pièces utilisées
        foreach ($this->parts as $part) {
            $article = Article::findOrFail($part['article_id']);
            
            RepairPart::create([
                'repair_id' => $this->repair_id,
                'article_id' => $part['article_id'],
                'quantity_used' => $part['quantity_used'],
                'unit_price' => $part['unit_price'] ?: $article->purchase_price,
                'total_price' => ($part['unit_price'] ?: $article->purchase_price) * $part['quantity_used'],
                'stock_location' => $part['stock_location'],
                'notes' => $part['notes'] ?: null,
            ]);

            // Créer le mouvement de sortie de stock
            StockMovement::create([
                'article_id' => $part['article_id'],
                'location' => $part['stock_location'],
                'type' => StockMovement::TYPE_EXIT,
                'quantity' => $part['quantity_used'],
                'reference' => 'REPAIR-' . $repair->id,
                'reason' => 'Pièce utilisée pour réparation',
                'repair_id' => $this->repair_id,
                'user_id' => Auth::id(),
            ]);

            // Mettre à jour le stock
            $stock = Stock::where('article_id', $part['article_id'])
                ->where('location', $part['stock_location'])
                ->first();
                
            if ($stock && $stock->quantity >= $part['quantity_used']) {
                $stock->decrement('quantity', $part['quantity_used']);
            }
        }

        // Enregistrer le matériel usé
        foreach ($this->used_materials as $material) {
            UsedMaterial::create([
                'repair_id' => $this->repair_id,
                'article_id' => $material['article_id'],
                'quantity' => $material['quantity'],
                'condition' => $material['condition'],
                'disposition' => $material['disposition'],
                'notes' => $material['notes'] ?: null,
            ]);
        }

        // Mettre à jour le coût total de la réparation
        $this->updateRepairCost($repair);

        $this->dispatch('notify', type: 'success', message: 'Utilisation du stock enregistrée.');
        $this->resetForm();
    }

    private function updateRepairCost(Repair $repair): void
    {
        $partsCost = $repair->repairParts()->sum('total_price');
        $repair->update(['parts_cost' => $partsCost]);
    }

    private function loadExistingData(): void
    {
        $repair = Repair::findOrFail($this->repair_id);
        
        // Charger les pièces existantes
        $this->parts = $repair->repairParts->map(function ($part) {
            return [
                'article_id' => $part->article_id,
                'quantity_used' => $part->quantity_used,
                'unit_price' => $part->unit_price,
                'stock_location' => $part->stock_location,
                'notes' => $part->notes,
            ];
        })->toArray();

        // Charger le matériel usé existant
        $this->used_materials = $repair->usedMaterials->map(function ($material) {
            return [
                'article_id' => $material->article_id,
                'quantity' => $material->quantity,
                'condition' => $material->condition,
                'disposition' => $material->disposition,
                'notes' => $material->notes,
            ];
        })->toArray();
    }

    private function resetForm(): void
    {
        $this->parts = [];
        $this->used_materials = [];
        $this->notes = '';
        $this->resetValidation();
    }


  

    public function render(): View
    {
        $query = Repair::with(['vehicle', 'garage', 'mechanic', 'repairParts.article', 'usedMaterials.article'])
            ->whereHas('repairParts')
            ->orWhereHas('usedMaterials')
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicle', function ($q2) {
                        $q2->where('registration', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $repairs = $query->paginate(20);

        $articles = Article::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'reference', 'purchase_price']);

        return view('livewire.portal.repairs.stock-usage', [
            'repairs' => $repairs,
            'articles' => $articles,
        ]);
    }
}
