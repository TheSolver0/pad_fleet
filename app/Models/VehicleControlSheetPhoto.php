<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;

class VehicleControlSheetPhoto extends Model
{
    protected $table = 'vehicle_control_sheet_photos';

    protected $fillable = [
        'control_sheet_id', 'type', 'file_path', 'original_filename',
        'file_size', 'mime_type', 'caption',
    ];

    public function controlSheet(): BelongsTo
    {
        return $this->belongsTo(VehicleControlSheet::class, 'control_sheet_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public static function storeUpload(VehicleControlSheet $sheet, UploadedFile $file, string $type, ?string $caption = null): self
    {
        $path = $file->store('control-sheets/' . $sheet->id, 'public');

        return self::create([
            'control_sheet_id'  => $sheet->id,
            'type'              => $type,
            'file_path'         => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'caption'           => $caption,
        ]);
    }
}
