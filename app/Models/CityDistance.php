<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CityDistance extends Model
{
    protected $fillable = ['from_city_id', 'to_city_id', 'distance_km'];

    protected function casts(): array
    {
        return ['distance_km' => 'integer'];
    }

    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * Distance connue entre deux villes (symétrique : A→B enregistré sert aussi pour B→A).
     */
    public static function between(int $cityIdA, int $cityIdB): ?int
    {
        if ($cityIdA === $cityIdB) {
            return 0;
        }

        return static::query()
            ->where(function ($q) use ($cityIdA, $cityIdB) {
                $q->where('from_city_id', $cityIdA)->where('to_city_id', $cityIdB);
            })
            ->orWhere(function ($q) use ($cityIdA, $cityIdB) {
                $q->where('from_city_id', $cityIdB)->where('to_city_id', $cityIdA);
            })
            ->value('distance_km');
    }
}
