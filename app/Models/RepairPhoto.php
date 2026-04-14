<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;

class RepairPhoto extends Model
{
    protected $fillable = [
        'repair_id', 'type', 'file_path', 'original_filename', 'file_size', 'mime_type', 'notes',
    ];

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public static function storeUpload(Repair $repair, UploadedFile $file, string $type): self
    {
        $path = $file->store('repairs/photos/' . $repair->id, 'public');

        return self::create([
            'repair_id'         => $repair->id,
            'type'              => $type,
            'file_path'         => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
        ]);
    }
}
