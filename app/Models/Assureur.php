<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assureur extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'contact_person', 'phone', 'email',
        'address', 'city', 'country', 'website', 'notes', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(Insurance::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
