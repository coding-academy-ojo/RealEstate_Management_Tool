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
        'water_company_id',
        'company_name',
        'company_name_ar',
        'meter_owner_name',
        'registration_number',
        'iron_number',
        'remarks',
        'is_active',
        'deactivation_reason',
        'deactivation_date',
        'initial_meter_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deactivation_date' => 'date',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function waterCompany(): BelongsTo
    {
        return $this->belongsTo(WaterCompany::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(WaterReading::class)->orderBy('reading_date')->orderBy('id');
    }

    public function latestReading(): HasOne
    {
        return $this->hasOne(WaterReading::class)->latestOfMany('reading_date');
    }

    public function deactivate(string $reason, ?string $date = null): void
    {
        $this->update([
            'is_active' => false,
            'deactivation_reason' => $reason,
            'deactivation_date' => $date ?? now()->toDateString(),
        ]);
    }

    public function reactivate(): void
    {
        $this->update([
            'is_active' => true,
            'deactivation_reason' => null,
            'deactivation_date' => null,
        ]);
    }
}
