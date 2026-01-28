<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use Auditable;

    protected $fillable = ['name', 'code'];

    public function vehicleModels(): HasMany
    {
        return $this->hasMany(VehicleModel::class, 'brand_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasManyThrough(Vehicle::class, VehicleModel::class, 'brand_id', 'vehicle_model_id');
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
