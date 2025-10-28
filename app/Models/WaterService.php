<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WaterService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'building_id',
        'company_name',
        'meter_owner_name',
        'registration_number',
        'iron_number',
        'remarks',
        'initial_meter_image',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(WaterReading::class)->orderBy('reading_date')->orderBy('id');
    }

    public function latestReading(): HasOne
    {
        return $this->hasOne(WaterReading::class)->latestOfMany('reading_date');
    }
}
