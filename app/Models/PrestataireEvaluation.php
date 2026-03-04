<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PrestataireEvaluation extends Model
{
    protected $table = 'prestataire_evaluations';

    protected $fillable = [
        'evaluable_type',
        'evaluable_id',
        'quality_score',
        'delivery_score',
        'reputation_score',
        'comment',
        'context',
        'evaluated_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'quality_score' => 'decimal:1',
            'delivery_score' => 'decimal:1',
            'reputation_score' => 'decimal:1',
            'evaluated_at' => 'date',
        ];
    }

    public const SCORE_MIN = 1;
    public const SCORE_MAX = 5;

    public function evaluable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Score moyen global (qualité + délais + réputation) */
    public function getOverallScoreAttribute(): float
    {
        $scores = array_filter([
            (float) $this->quality_score,
            $this->delivery_score ? (float) $this->delivery_score : null,
            $this->reputation_score ? (float) $this->reputation_score : null,
        ]);
        return count($scores) ? round(array_sum($scores) / count($scores), 1) : 0;
    }
}
