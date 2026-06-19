<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceContractGlobal extends Model
{
    use Auditable;

    protected $table = 'insurance_contract_globals';

    protected $fillable = [
        'name', 'assureur_id', 'insurer', 'lot_description', 'start_date', 'end_date',
        'optional_prime', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'optional_prime' => 'decimal:2',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InsuranceContractGlobalDocument::class, 'insurance_contract_global_id');
    }

    public function assureur(): BelongsTo
    {
        return $this->belongsTo(Assureur::class);
    }

    public function isActive(): bool
    {
        return $this->start_date->isPast() || $this->start_date->isToday()
            ? ($this->end_date->isFuture() || $this->end_date->isToday())
            : false;
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date->isFuture() && $this->end_date->diffInDays(now(), false) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now());
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
