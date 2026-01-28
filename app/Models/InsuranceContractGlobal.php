<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceContractGlobal extends Model
{
    use Auditable;

    protected $table = 'insurance_contract_globals';

    protected $fillable = [
        'name', 'insurer', 'lot_description', 'start_date', 'end_date',
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

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'insurance_contract_global_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InsuranceContractGlobalDocument::class, 'insurance_contract_global_id');
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date->isFuture() && $this->end_date->diffInDays(now(), false) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
