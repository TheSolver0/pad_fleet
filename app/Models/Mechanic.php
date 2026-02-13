<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mechanic extends Model
{
    use Auditable;

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'address',
        'specialization', 'hire_date', 'certificate', 'hourly_rate',
        'is_active', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'hourly_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
