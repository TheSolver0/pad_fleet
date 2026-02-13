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

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'vehicle_id', 'garage_id', 'mechanic_id', 'type', 'transfer_sheet_path',
        'description', 'cost', 'started_at', 'completed_at', 'notes',
        'repair_type', 'priority', 'estimated_duration',
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

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'Basse',
            self::PRIORITY_MEDIUM => 'Moyenne',
            self::PRIORITY_HIGH => 'Haute',
            self::PRIORITY_URGENT => 'Urgente',
            default => $this->priority,
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'success',
            self::PRIORITY_MEDIUM => 'info',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger',
            default => 'secondary',
        };
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
