<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repair extends Model
{
    use Auditable;

    public const TYPE_INTERNAL = 'internal';
    public const TYPE_EXTERNAL = 'external';

    protected $fillable = [
        'vehicle_id', 'garage_id', 'mechanic_id', 'type', 'transfer_sheet_path',
        'description', 'cost', 'started_at', 'completed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function repairParts(): HasMany
    {
        return $this->hasMany(RepairPart::class);
    }

    public function usedMaterials(): HasMany
    {
        return $this->hasMany(UsedMaterial::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === self::TYPE_INTERNAL ? 'Interne' : 'Externe';
    }

    public function getPartsCostAttribute(): float
    {
        return $this->repairParts()->sum('total_price');
    }

    public function getTotalCostAttribute(): float
    {
        return $this->cost + $this->parts_cost;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
