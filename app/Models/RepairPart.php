<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairPart extends Model
{
    use Auditable;

    protected $fillable = [
        'repair_id', 'article_id', 'quantity_used', 'unit_price',
        'total_price', 'stock_location', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function getStockLocationLabelAttribute(): string
    {
        return $this->stock_location === 'main' ? 'Magasin principal' : 'Magasin garage';
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
