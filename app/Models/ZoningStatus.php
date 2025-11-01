<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\LogsActivity;

class ZoningStatus extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Sites that have this zoning status
     */
    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'site_zoning_status')
            ->withTimestamps();
    }

    /**
     * Scope to get only active zoning statuses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
