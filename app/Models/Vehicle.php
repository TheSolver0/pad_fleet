<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use Auditable;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_IN_USE = 'in_use';
    public const STATUS_REPAIR = 'repair';
    public const STATUS_OUT_OF_SERVICE = 'out_of_service';

    public const CATEGORY_LEGER = 'leger';
    public const CATEGORY_UTILITAIRE = 'utilitaire';
    public const CATEGORY_LOURD = 'lourd';
    public const CATEGORY_MOTO = 'moto';
    public const CATEGORY_BUS = 'bus';
    public const CATEGORY_4X4 = '4x4';
    public const CATEGORY_CAMIONNETTE = 'camionnette';
    public const CATEGORY_OTHER = 'autre';

    /** Libellés des catégories pour les selects */
    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_LEGER => 'Léger',
            self::CATEGORY_UTILITAIRE => 'Utilitaire',
            self::CATEGORY_CAMIONNETTE => 'Camionnette',
            self::CATEGORY_4X4 => '4×4 / Tout-terrain',
            self::CATEGORY_LOURD => 'Lourd / Poids lourd',
            self::CATEGORY_BUS => 'Bus / Minibus',
            self::CATEGORY_MOTO => 'Moto / Deux-roues',
            self::CATEGORY_OTHER => 'Autre',
        ];
    }

    public function getCategoryLabelAttribute(): ?string
    {
        return $this->category ? (self::categoryOptions()[$this->category] ?? $this->category) : null;
    }

    /** Types d'affectation véhicule → personne */
    public const ASSIGNMENT_DOTATION = 'dotation';
    public const ASSIGNMENT_AFFECTATION = 'affectation';
    public const ASSIGNMENT_LIAISON = 'liaison';
    public const ASSIGNMENT_LUCATELLI = 'lucatelli';
    public const ASSIGNMENT_SEC_SURETE = 'sec_surete';
    public const ASSIGNMENT_TRAVAUX = 'travaux';
    public const ASSIGNMENT_MISSIONS = 'missions';
    public const ASSIGNMENT_TRANSPORT_VIP = 'transport_vip';

    public static function assignmentTypeOptions(): array
    {
        return [
            self::ASSIGNMENT_DOTATION => 'Dotation',
            self::ASSIGNMENT_AFFECTATION => 'Affectation',
            self::ASSIGNMENT_LIAISON => 'Liaison',
            self::ASSIGNMENT_LUCATELLI => 'LUCATELLI',
            self::ASSIGNMENT_SEC_SURETE => 'Séc. / Sûreté',
            self::ASSIGNMENT_TRAVAUX => 'Travaux',
            self::ASSIGNMENT_MISSIONS => 'Missions',
            self::ASSIGNMENT_TRANSPORT_VIP => 'Transport hôtes VIP',
        ];
    }

    public function getAssignmentTypeLabelAttribute(): ?string
    {
        return $this->assignment_type ? (self::assignmentTypeOptions()[$this->assignment_type] ?? $this->assignment_type) : null;
    }

    /** Types pour lesquels la période est typiquement indéfinie (ex. Dotation) */
    public static function assignmentTypesIndefiniteByDefault(): array
    {
        return [self::ASSIGNMENT_DOTATION];
    }

    public function isAssignmentPeriodIndefinite(): bool
    {
        return $this->assignment_end_at === null && $this->assigned_person_id !== null;
    }

    /** Libellé période d'affectation pour affichage */
    public function getAssignmentPeriodLabelAttribute(): ?string
    {
        if (!$this->assigned_person_id) {
            return null;
        }
        if ($this->assignment_end_at === null) {
            return $this->assignment_start_at
                ? 'À partir du ' . $this->assignment_start_at->format('d/m/Y') . ' (indéfini)'
                : 'Indéfini';
        }
        $from = $this->assignment_start_at ? $this->assignment_start_at->format('d/m/Y') : '?';
        return $from . ' — ' . $this->assignment_end_at->format('d/m/Y');
    }

    /** Années pour amortissement linéaire valeur vénale */
    private const VENAL_DEPRECIATION_YEARS = 8;

    protected $fillable = [
        'registration', 'vehicle_model_id', 'category', 'purchase_date', 'purchase_price',
        'venal_value', 'mileage', 'power', 'status', 'garage_id', 'insurance_contract_global_id',
        'assigned_person_id', 'assignment_type', 'assignment_start_at', 'assignment_end_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'assignment_start_at' => 'date',
            'assignment_end_at' => 'date',
            'purchase_price' => 'decimal:2',
            'venal_value' => 'decimal:2',
            'mileage' => 'integer',
            'power' => 'integer',
        ];
    }

    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function insuranceContractGlobal(): BelongsTo
    {
        return $this->belongsTo(InsuranceContractGlobal::class, 'insurance_contract_global_id');
    }

    public function assignedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'assigned_person_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('sort_order');
    }

    public function carteGrises(): HasMany
    {
        return $this->hasMany(VehicleCarteGrise::class)->orderByDesc('issued_at');
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    public function sinistres(): HasMany
    {
        return $this->hasMany(Sinistre::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    /** Calcule la valeur vénale par amortissement linéaire (sur 8 ans par défaut). */
    public function computeVenalValue(): ?float
    {
        if (!$this->purchase_price || !$this->purchase_date) {
            return null;
        }
        $years = $this->purchase_date->diffInYears(now());
        if ($years >= self::VENAL_DEPRECIATION_YEARS) {
            return 0.0;
        }
        $remaining = 1 - ($years / self::VENAL_DEPRECIATION_YEARS);
        return round((float) $this->purchase_price * $remaining, 2);
    }

    /** Met à jour venal_value avec le calcul automatique. */
    public function updateVenalValue(): void
    {
        $value = $this->computeVenalValue();
        if ($value !== null) {
            $this->update(['venal_value' => $value]);
        }
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'Disponible',
            self::STATUS_IN_USE => 'En mission',
            self::STATUS_REPAIR => 'En réparation',
            self::STATUS_OUT_OF_SERVICE => 'Hors service',
            default => $this->status,
        };
    }

    protected static function booted(): void
    {
        static::bootAuditable();
        static::saving(function (self $vehicle) {
            if ($vehicle->isDirty(['purchase_date', 'purchase_price']) && $vehicle->purchase_price && $vehicle->purchase_date) {
                $vehicle->venal_value = $vehicle->computeVenalValue();
            }
        });
    }
}
