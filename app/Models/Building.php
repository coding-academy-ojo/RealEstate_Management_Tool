<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Building extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'name',
        'area_m2',
        'has_building_permit',
        'building_permit_file',
        'has_occupancy_permit',
        'occupancy_permit_file',
        'has_profession_permit',
        'profession_permit_file',
        'as_built_drawing',
        'remarks',
        // يُملأ تلقائيًا:
        // 'sequence', 'code'
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
        'has_building_permit'   => 'boolean',
        'has_occupancy_permit'  => 'boolean',
        'has_profession_permit' => 'boolean',
    ];

    protected static function booted()
    {
        // عند الإنشاء: احسب sequence داخل الموقع ثم ابنِ الكود من كود الموقع
        static::creating(function (Building $building) {
            $site = $building->site ?? Site::find($building->site_id);
            if (!$site) {
                throw new \RuntimeException('Site is required to create a building.');
            }

            $nextSeq = (int) ($site->buildings()->max('sequence') ?? 0) + 1;
            $building->sequence = $nextSeq;
            $building->code = $site->code . str_pad((string)$nextSeq, 2, '0', STR_PAD_LEFT);
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class)->withTrashed();
    }

    public function lands(): BelongsToMany
    {
        return $this->belongsToMany(Land::class, 'building_land')
            ->withTimestamps();
    }

    /**
     * Get all images for the building.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    /**
     * Get the primary image for the building.
     */
    public function primaryImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    public function waterServices(): HasMany
    {
        return $this->hasMany(WaterService::class);
    }

    public function electricityServices(): HasMany
    {
        return $this->hasMany(ElectricityService::class);
    }

    public function reInnovations(): MorphMany
    {
        return $this->morphMany(ReInnovation::class, 'innovatable');
    }
}
