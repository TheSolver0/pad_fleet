<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Garage extends Model
{
    use Auditable;

    public const TYPE_INTERNAL = 'internal';
    public const TYPE_EXTERNAL = 'external';

    protected $fillable = [
        'name', 'type', 'address', 'phone', 'email', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function sinistres(): HasMany
    {
        return $this->hasMany(Sinistre::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
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
        $max = $this->evaluations()->max('evaluated_at');
        return $max ? \Carbon\Carbon::parse($max) : null;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === self::TYPE_INTERNAL ? 'Interne' : 'Externe';
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
