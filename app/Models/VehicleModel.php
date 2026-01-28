<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleModel extends Model
{
    use Auditable;

    protected $table = 'vehicle_models';

    protected $fillable = ['brand_id', 'name', 'code'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_model_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->brand ? $this->brand->name . ' ' . $this->name : $this->name;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
