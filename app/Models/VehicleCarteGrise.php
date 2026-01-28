<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VehicleCarteGrise extends Model
{
    protected $table = 'vehicle_carte_grise';

    protected $fillable = [
        'vehicle_id', 'reference_number', 'issued_at', 'expires_at',
        'file_path', 'original_name', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public static function createForVehicle(Vehicle $vehicle, array $data, ?UploadedFile $file = null): self
    {
        $path = null;
        $originalName = null;
        if ($file) {
            $path = $file->store('vehicles/' . $vehicle->id . '/carte-grise', 'public');
            $originalName = $file->getClientOriginalName();
        }
        return $vehicle->carteGrises()->create([
            'reference_number' => $data['reference_number'] ?? null,
            'issued_at' => isset($data['issued_at']) ? \Carbon\Carbon::parse($data['issued_at']) : null,
            'expires_at' => isset($data['expires_at']) ? \Carbon\Carbon::parse($data['expires_at']) : null,
            'file_path' => $path,
            'original_name' => $originalName,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function delete(): ?bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
        return parent::delete();
    }
}
