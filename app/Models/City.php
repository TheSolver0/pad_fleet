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
     * Génère automatiquement un code à partir du nom si non fourni
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                // Générer un code à partir du nom : utiliser 3 premières lettres en majuscules
                $baseCode = strtoupper(substr($model->name, 0, 3));
                $code = $baseCode;
                $count = 1;

                // Vérifier l'unicité et ajouter un suffixe si nécessaire
                while (self::where('code', $code)->exists()) {
                    $code = $baseCode . $count;
                    $count++;
                }

                $model->code = $code;
            } else {
                // Si le code est fourni, s'assurer qu'il est unique
                $originalCode = $model->code;
                $count = 1;
                while (self::where('code', $model->code)->exists()) {
                    $model->code = $originalCode . $count;
                    $count++;
                }
            }
        });
    }

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
