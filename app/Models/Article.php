<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'reference',
        'name',
        'description',
        'article_category_id',
        'brand',
        'model',
        'unit',
        'purchase_price',
        'min_stock_level',
        'max_stock_level',
        'tire_size',
        'compatible_vehicle_categories',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'compatible_vehicle_categories' => 'array',
        'purchase_price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function repairParts(): HasMany
    {
        return $this->hasMany(RepairPart::class);
    }

    public function usedMaterials(): HasMany
    {
        return $this->hasMany(UsedMaterial::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(ArticlePriceHistory::class);
    }

    // Accessors pour la gestion de stock
    public function getTotalStockAttribute(): int
    {
        return $this->stocks()->sum('quantity');
    }

    public function getMainStockAttribute(): int
    {
        return $this->stocks()->where('location', 'main')->sum('quantity');
    }

    public function getGarageStockAttribute(): int
    {
        return $this->stocks()->where('location', 'garage')->sum('quantity');
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->stocks()->sum('quantity') - $this->stocks()->sum('reserved_quantity');
    }

    public function getCompatibleVehicleCategoriesLabelAttribute(): string
    {
        if (empty($this->compatible_vehicle_categories)) {
            return 'Tous';
        }

        $labels = [];
        foreach ($this->compatible_vehicle_categories as $category) {
            $labels[] = match($category) {
                'leger' => 'Léger',
                'utilitaire' => 'Utilitaire',
                'camionnette' => 'Camionnette',
                '4x4' => '4x4',
                'lourd' => 'Lourd',
                'bus' => 'Bus',
                'moto' => 'Moto',
                'autre' => 'Autre',
                default => $category,
            };
        }

        return implode(', ', $labels);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    // Vérification de compatibilité pneus
    public function isTireCompatibleWithVehicle(Vehicle $vehicle): bool
    {
        if (empty($this->tire_size) || empty($this->compatible_vehicle_categories)) {
            return false;
        }

        return in_array($vehicle->category, $this->compatible_vehicle_categories);
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
