<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'region',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the display name with region.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->region})";
    }

    /**
     * Scope a query to only include active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by region.
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Get cities grouped by region.
     */
    public static function getGroupedByRegion(): array
    {
        return self::active()
            ->orderBy('region')
            ->orderBy('name')
            ->get()
            ->groupBy('region')
            ->map(function ($cities) {
                return $cities->pluck('name', 'id')->toArray();
            })
            ->toArray();
    }
}
