<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use Auditable;

    protected $fillable = [
        'article_id', 'location', 'quantity', 'reserved_quantity'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'available_quantity' => 'integer',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getLocationLabelAttribute(): string
    {
        return $this->location === 'main' ? 'Magasin principal' : 'Magasin garage';
    }

    public function isLowStock(): bool
    {
        return $this->available_quantity < $this->article->min_stock_level;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
