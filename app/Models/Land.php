<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Land extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        // Location Information (in order)
        'governorate',          // المحافظة
        'directorate',          // المديرية
        'directorate_number',   // رقم المديرية
        'village',              // القرية
        'village_number',       // رقم القرية
        'basin',                // الحوض
        'basin_number',         // رقم الحوض
        'neighborhood',         // الحي
        'neighborhood_number',  // رقم الحي
        'plot_number',          // رقم القطعة
        'plot_key',             // مفتاح القطعة
        // Area and other details
        'area_m2',              // مساحة القطعة
        'region',               // REGION
        'zoning',               // التنظيم
        'land_directorate',     // مديرية الأراضي
        // Documents and media
        'ownership_doc',        // سند الملكية (PDF, JPG)
        'site_plan',            // مخطط الموقع (PDF, JPG)
        'zoning_plan',          // مخطط تنظيمي (PDF, JPG)
        'photos',               // صور الموقع (20 images)
        // Map location
        'map_location',         // Full Google Maps URL
        'latitude',             // Extracted latitude
        'longitude',            // Extracted longitude
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /*========= Relations =========*/
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class)->withTrashed();
    }

    // المباني المرتبطة بهذه القطعة (many-to-many)
    public function buildings(): BelongsToMany
    {
        return $this->belongsToMany(Building::class, 'building_land')
            ->withTimestamps();
    }

    /**
     * Get all images for the land.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    /**
     * Get the primary image for the land.
     */
    public function primaryImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    public function rennovations(): MorphMany
    {
        return $this->morphMany(Rennovation::class, 'innovatable');
    }
}
