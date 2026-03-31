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
        'description', 'cost', 'started_at', 'completed_at', 'expected_completed_at',
        'quality_rating', 'delay_rating', 'evaluation_comment', 'evaluated_at',
        'notes', 'repair_type', 'priority', 'estimated_duration',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'expected_completed_at' => 'datetime',
            'quality_rating' => 'decimal:1',
            'delay_rating' => 'decimal:1',
            'evaluated_at' => 'datetime',
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

    public function expenses(): HasMany
    {
        return $this->hasMany(RepairExpense::class);
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
        return (float) $this->cost + (float) $this->parts_cost + (float) $this->expenses()->sum('amount');
    }

    /** Délai donné au prestataire (jours) — entre début et date limite prévue */
    public function getDelaiDonneJoursAttribute(): ?int
    {
        if (! $this->started_at || ! $this->expected_completed_at) {
            return null;
        }
        return (int) $this->started_at->diffInDays($this->expected_completed_at, false);
    }

    /** Délai réalisé par le prestataire (jours) — entre début et fin réelle */
    public function getDelaiRealiseJoursAttribute(): ?int
    {
        if (! $this->started_at || ! $this->completed_at) {
            return null;
        }
        return (int) $this->started_at->diffInDays($this->completed_at, false);
    }

    /** Résumé délai pour affichage */
    public function getDelaiResumeAttribute(): ?string
    {
        $donne = $this->delai_donne_jours;
        $realise = $this->delai_realise_jours;
        if ($donne === null && $realise === null) {
            return null;
        }
        $parts = [];
        if ($donne !== null) {
            $parts[] = 'Donné: ' . $donne . ' j';
        }
        if ($realise !== null) {
            $parts[] = 'Fait: ' . $realise . ' j';
        }
        return implode(' — ', $parts);
    }

    public function getIsEvaluatedAttribute(): bool
    {
        return $this->evaluated_at !== null;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
