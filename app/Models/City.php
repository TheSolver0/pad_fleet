<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'region_id',
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
        $region = $this->regionRelation?->name ?? $this->region;
        return "{$this->name} ({$region})";
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
        if (is_numeric($region)) {
            return $query->where('region_id', (int) $region);
        }

        return $query->where(function ($q) use ($region) {
            $q->where('region', $region)->orWhereHas('regionRelation', fn ($r) => $r->where('name', $region));
        });
    }

    public function regionRelation(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Get cities grouped by region.
     */
    public static function getGroupedByRegion(): array
    {
        return self::active()
            ->with('regionRelation:id,name')
            ->orderBy('region')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($city) => $city->regionRelation?->name ?? $city->region)
            ->map(function ($cities) {
                return $cities->pluck('name', 'id')->toArray();
            })
            ->toArray();
    }
}
