<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VehicleDocument extends Model
{
    public const TYPE_ASSURANCE = 'assurance';
    public const TYPE_CARTE_GRISE = 'carte_grise';
    public const TYPE_OTHER = 'autre';

    protected $fillable = ['vehicle_id', 'type', 'file_path', 'original_name', 'expires_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'date'];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public static function storeUpload(Vehicle $vehicle, UploadedFile $file, string $type, ?string $expiresAt = null): self
    {
        $path = $file->store('vehicles/' . $vehicle->id . '/documents', 'public');
        return $vehicle->documents()->create([
            'type' => $type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'expires_at' => $expiresAt ? \Carbon\Carbon::parse($expiresAt) : null,
        ]);
    }

    public function delete(): ?bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
        return parent::delete();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ASSURANCE => 'Assurance',
            self::TYPE_CARTE_GRISE => 'Carte grise',
            default => 'Autre',
        };
    }
}
