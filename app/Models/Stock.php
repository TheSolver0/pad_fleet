<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use Auditable;

    protected $fillable = [
        'article_id', 'location', 'quantity', 'reserved_quantity',
    ];

    protected function quantity(): Attribute
    {
        return Attribute::get(fn (mixed $value): int|float => $this->normaliseQuantity($value));
    }

    protected function reservedQuantity(): Attribute
    {
        return Attribute::get(fn (mixed $value): int|float => $this->normaliseQuantity($value));
    }

    protected function availableQuantity(): Attribute
    {
        return Attribute::get(fn (mixed $value): int|float => $this->normaliseQuantity($value));
    }

    private function normaliseQuantity(mixed $value): int|float
    {
        $quantity = (float) $value;

        return floor($quantity) === $quantity ? (int) $quantity : $quantity;
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
