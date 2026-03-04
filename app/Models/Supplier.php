<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    use Auditable;

    protected $fillable = [
        'name', 'code', 'phone', 'email', 'address', 
        'contact_person', 'contact_phone', 'notes', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function evaluations(): MorphMany
    {
        return $this->morphMany(PrestataireEvaluation::class, 'evaluable')->orderByDesc('evaluated_at');
    }

    public function getAverageQualityScoreAttribute(): ?float
    {
        $avg = $this->evaluations()->avg('quality_score');
        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getAverageDeliveryScoreAttribute(): ?float
    {
        $avg = $this->evaluations()->whereNotNull('delivery_score')->avg('delivery_score');
        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getAverageReputationScoreAttribute(): ?float
    {
        $avg = $this->evaluations()->whereNotNull('reputation_score')->avg('reputation_score');
        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getOverallRatingAttribute(): ?float
    {
        $scores = array_filter([
            $this->average_quality_score,
            $this->average_delivery_score,
            $this->average_reputation_score,
        ]);
        return count($scores) ? round(array_sum($scores) / count($scores), 1) : null;
    }

    public function getLastEvaluatedAtAttribute(): ?\Carbon\Carbon
    {
        return $this->evaluations()->max('evaluated_at')
            ? \Carbon\Carbon::parse($this->evaluations()->max('evaluated_at'))
            : null;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
