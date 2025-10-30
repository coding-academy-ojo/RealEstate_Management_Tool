<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'title',
        'description',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'size' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the parent imageable model (Site, Building, or Land).
     */
    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL of the image.
     */
    public function getUrlAttribute()
    {
        // Use the images.show route for private storage access
        return route('images.show', $this->id);
    }

    /**
     * Get the file size in a human-readable format.
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Delete image file when model is deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($image) {
            // Try deleting from both disks
            foreach (['private', 'public'] as $disk) {
                if (Storage::disk($disk)->exists($image->path)) {
                    Storage::disk($disk)->delete($image->path);
                }
            }
        });
    }
}
