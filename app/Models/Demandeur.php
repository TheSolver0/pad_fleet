<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demandeur extends Model
{
    use Auditable;

    public const TYPE_PERSON = 'person';
    public const TYPE_DIRECTION = 'direction';

    protected $fillable = [
        'matricule', 'name', 'demandeur_type', 'service_id', 'person_id', 'direction_id', 'contact_phone', 'contact_email', 'notes',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class, 'demandeur_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->demandeur_type === self::TYPE_DIRECTION ? 'Direction' : 'Personne';
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
