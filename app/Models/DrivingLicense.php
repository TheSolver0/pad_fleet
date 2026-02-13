<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrivingLicense extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'driver_id',
        'license_number',
        'license_type',
        'category',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'issuing_country',
        'is_active',
        'notes',
        'file_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    // Accesseurs pour les labels
    public function getLicenseTypeLabelAttribute(): string
    {
        return match($this->license_type) {
            'A' => 'Moto',
            'B' => 'Voiture',
            'C' => 'Poids lourd',
            'D' => 'Autobus',
            'E' => 'Remorque',
            'F' => 'Agricole',
            'G' => 'Engin spécial',
            'H' => 'Transport en commun',
            'I' => 'Transport de marchandises',
            default => $this->license_type,
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'A1' => 'Moto légère',
            'A2' => 'Moto intermédiaire',
            'A' => 'Moto',
            'B1' => 'Voiture légère',
            'B' => 'Voiture',
            'C1' => 'Poids lourd léger',
            'C' => 'Poids lourd',
            'D1' => 'Autobus léger',
            'D' => 'Autobus',
            'E' => 'Remorque',
            'F' => 'Véhicule agricole',
            'G' => 'Engin spécial',
            'H' => 'Transport en commun',
            'I' => 'Transport marchandises',
            default => $this->category,
        };
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactif';
        }
        
        if ($this->expiry_date < now()) {
            return 'Expiré';
        }
        
        if ($this->expiry_date->diffInDays(now()) <= 30) {
            return 'Expiration proche';
        }
        
        return 'Valide';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Valide' => 'success',
            'Expiration proche' => 'warning',
            'Expiré' => 'danger',
            'Inactif' => 'secondary',
            default => 'secondary',
        };
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date->diffInDays(now()) <= 30 && $this->expiry_date >= now();
    }

    public function isExpired(): bool
    {
        return $this->expiry_date < now();
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
