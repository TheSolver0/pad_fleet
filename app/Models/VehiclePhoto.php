<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VehiclePhoto extends Model
{
    protected $fillable = ['vehicle_id', 'file_path', 'original_name', 'caption', 'sort_order', 'taken_at'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'taken_at' => 'date',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public static function storeUpload(Vehicle $vehicle, UploadedFile $file, ?string $caption = null, ?string $takenAt = null): self
    {
        $path = $file->store('vehicles/' . $vehicle->id . '/photos', 'public');
        $maxOrder = $vehicle->photos()->max('sort_order') ?? 0;
        return $vehicle->photos()->create([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'caption' => $caption,
            'sort_order' => $maxOrder + 1,
            'taken_at' => $takenAt ? \Carbon\Carbon::parse($takenAt) : null,
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
