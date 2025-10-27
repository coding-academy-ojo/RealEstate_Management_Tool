<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cluster_no',
        'governorate',
        'region',
        'name',
        'area_m2',
        'zoning_status',
        'notes',
        'other_documents',
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
        'other_documents' => 'array',
    ];

    protected static function booted()
    {
        // Auto-set region based on governorate before creating
        static::creating(function (Site $site) {
            // Auto-calculate region from governorate
            $site->region = self::getRegionFromGovernorate($site->governorate);

            // Set default cluster_no if not provided
            $site->cluster_no = $site->cluster_no ?: 1;

            // Get next serial number within the same governorate
            $nextSerial = (int) (static::where('governorate', $site->governorate)->max('serial_no') ?? 0) + 1;
            $site->serial_no = $nextSerial;

            // Generate code: [Region][Governorate][Serial]
            $site->code = self::makeCode($site->region, $site->governorate, $site->serial_no);
        });

        // Update region and code when governorate or cluster changes
        static::updating(function (Site $site) {
            $dirtyGov = $site->isDirty('governorate');
            $dirtyCluster = $site->isDirty('cluster_no');

            if ($dirtyGov || $dirtyCluster) {
                DB::transaction(function () use ($site, $dirtyGov) {
                    // Auto-update region when governorate changes
                    if ($dirtyGov) {
                        $site->region = self::getRegionFromGovernorate($site->governorate);
                        $site->serial_no = (int) (self::where('governorate', $site->governorate)->max('serial_no') ?? 0) + 1;
                    }

                    // Rebuild code with new region and governorate
                    $site->code = self::makeCode($site->region, $site->governorate, $site->serial_no);
                });
            }
        });

        // Update building codes after site code changes
        static::updated(function (Site $site) {
            if ($site->wasChanged(['governorate', 'region', 'cluster_no', 'serial_no', 'code'])) {
                // Update all building codes to match new site code
                foreach ($site->buildings as $b) {
                    $b->code = $site->code . str_pad((string)$b->sequence, 2, '0', STR_PAD_LEFT);
                    $b->save();
                }
            }
        });
    }

    /**
     * Determine region number from governorate code
     * Region 1 (Capital): AM
     * Region 2 (North): IR, MF, AJ, JA
     * Region 3 (Middle): BA, ZA, MA
     * Region 4 (South): AQ, KA, TF, MN
     */
    public static function getRegionFromGovernorate(string $governorate): int
    {
        return match ($governorate) {
            'AM' => 1,                          // Capital
            'IR', 'MF', 'AJ', 'JA' => 2,        // North
            'BA', 'ZA', 'MA' => 3,              // Middle
            'AQ', 'KA', 'TF', 'MN' => 4,        // South
            default => 1,
        };
    }

    public static function makeCode(int $region, string $gov, int $serial): string
    {
        return $region . $gov . str_pad((string)$serial, 3, '0', STR_PAD_LEFT);
    }

    public function lands(): HasMany
    {
        return $this->hasMany(Land::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function reInnovations(): MorphMany
    {
        return $this->morphMany(ReInnovation::class, 'innovatable');
    }

    public function zoningStatuses(): BelongsToMany
    {
        return $this->belongsToMany(ZoningStatus::class, 'site_zoning_status')
            ->withTimestamps();
    }

    /**
     * Get all images for the site.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    /**
     * Get the primary image for the site.
     */
    public function primaryImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    // اسم المحافظة بالعربي (اختياري للعرض)
    public function getGovernorateNameAttribute(): ?string
    {
        return match ($this->governorate) {
            'AM' => 'عمّان',
            'IR' => 'إربد',
            'AJ' => 'عجلون',
            'JA' => 'جرش',
            'MA' => 'مادبا',
            'BA' => 'البلقاء',
            'ZA' => 'الزرقاء',
            'KA' => 'الكرك',
            'TF' => 'الطفيلة',
            'MN' => 'معان',
            'AQ' => 'العقبة',
            'MF' => 'المفرق',
            default => null
        };
    }

    // Governorate name in English
    public function getGovernorateNameEnAttribute(): ?string
    {
        return match ($this->governorate) {
            'AM' => 'Amman',
            'IR' => 'Irbid',
            'AJ' => 'Ajloun',
            'JA' => 'Jerash',
            'MA' => 'Madaba',
            'BA' => 'Balqa',
            'ZA' => 'Zarqa',
            'KA' => 'Karak',
            'TF' => 'Tafileh',
            'MN' => 'Ma\'an',
            'AQ' => 'Aqaba',
            'MF' => 'Mafraq',
            default => null
        };
    }

    // Region name accessor
    public function getRegionNameAttribute(): string
    {
        return match ($this->region) {
            1 => 'Capital',
            2 => 'North',
            3 => 'Middle',
            4 => 'South',
            default => 'Unknown'
        };
    }
}
