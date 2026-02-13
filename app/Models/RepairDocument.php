<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairDocument extends Model
{
    use HasFactory;
    use Auditable;

    const TYPE_DIAGNOSTIC = 'diagnostic';
    const TYPE_ESTIMATE = 'estimate';
    const TYPE_INVOICE = 'invoice';
    const TYPE_TECHNICAL_REPORT = 'technical_report';
    const TYPE_OTHER = 'other';

    protected $fillable = [
        'repair_id',
        'document_type',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'physically_validated',
        'physically_validated_by',
        'physically_validated_at',
        'digitally_validated',
        'digitally_validated_by',
        'digitally_validated_at',
        'validation_notes',
    ];

    protected $casts = [
        'physically_validated' => 'boolean',
        'digitally_validated' => 'boolean',
        'physically_validated_at' => 'datetime',
        'digitally_validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function physicallyValidatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'physically_validated_by');
    }

    public function digitallyValidatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'digitally_validated_by');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isFullyValidated(): bool
    {
        return $this->physically_validated && $this->digitally_validated;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
