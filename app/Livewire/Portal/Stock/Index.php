<?php

namespace App\Livewire\Portal\Stock;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category_filter = '';
    public string $location_filter = '';
    public string $stock_status = 'all'; // 'all', 'low', 'out'
    protected $paginationTheme = 'bootstrap'; 

    public function render(): View
    {
        $query = Stock::with(['article.category'])
            ->select('stocks.*')
            ->join('articles', 'stocks.article_id', '=', 'articles.id')
            ->join('article_categories', 'articles.article_category_id', '=', 'article_categories.id');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('articles.reference', 'like', '%' . $this->search . '%')
                    ->orWhere('articles.name', 'like', '%' . $this->search . '%')
                    ->orWhere('articles.brand', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category_filter !== '') {
            $query->where('articles.article_category_id', $this->category_filter);
        }

        if ($this->location_filter !== '') {
            $query->where('stocks.location', $this->location_filter);
        }

        if ($this->stock_status === 'low') {
            $query->whereRaw('(stocks.quantity - stocks.reserved_quantity) <= articles.min_stock_level');
        } elseif ($this->stock_status === 'out') {
            $query->where('stocks.quantity', '<=', 0);
        }

        $stocks = $query->orderBy('article_categories.name')
            ->orderBy('articles.name')
            ->paginate(20);

        $categories = ArticleCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.portal.stock.index', [
            'stocks' => $stocks,
            'categories' => $categories,
            'getStockStatusLabel' => fn(Stock $s) => $this->getStockStatusLabel($s),
        ]);
    }

    public function getLocationLabel(string $location): string
    {
        return $location === 'main' ? 'Magasin principal' : 'Magasin garage';
    }

    public function getStockStatusLabel(Stock $stock): string
    {
        if ($stock->quantity <= 0) {
            return '<span class="badge bg-danger">Rupture</span>';
        } elseif ($stock->available_quantity <= $stock->article->min_stock_level) {
            return '<span class="badge bg-warning">Stock faible</span>';
        } else {
            return '<span class="badge bg-success">En stock</span>';
        }
    }
}
