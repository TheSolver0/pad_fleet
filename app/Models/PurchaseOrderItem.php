<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use Auditable;

    protected $fillable = [
        'purchase_order_id', 'article_id', 'quantity', 'unit_price',
        'total_price', 'quantity_received', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'quantity_received' => 'integer',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function getRemainingQuantityAttribute(): int
    {
        return $this->quantity - $this->quantity_received;
    }

    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
