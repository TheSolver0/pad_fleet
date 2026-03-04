<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use Auditable;

    public const TYPE_ENTRY = 'entry';
    public const TYPE_EXIT = 'exit';
    public const TYPE_TRANSFER = 'transfer';

    protected $fillable = [
        'article_id', 'location', 'type', 'quantity', 'reference',
        'reason', 'supplier_id', 'purchase_order_id', 'repair_id', 'user_id'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ENTRY => 'Réception / Entrée',
            self::TYPE_EXIT => 'Consommation / Sortie',
            self::TYPE_TRANSFER => 'Transfert',
            default => $this->type,
        };
    }

    public function getLocationLabelAttribute(): string
    {
        return $this->location === 'main' ? 'Magasin principal' : 'Magasin garage';
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
