<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class ElectricityService extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'building_id',
        'electricity_company_id',
        'subscriber_name',
        'meter_number',
        'has_solar_power',
        'is_active',
        'deactivation_reason',
        'deactivation_date',
        'company_name',
        'company_name_ar',
        'registration_number',
        'reset_file',
        'remarks',
    ];

    protected $casts = [
        'has_solar_power'  => 'boolean',
        'is_active'  => 'boolean',
        'deactivation_date' => 'date',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function electricityCompany(): BelongsTo
    {
        return $this->belongsTo(ElectricityCompany::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(ElectricReading::class, 'electric_service_id')
            ->orderBy('reading_date')
            ->orderBy('id');
    }

    public function latestReading(): HasOne
    {
        return $this->hasOne(ElectricReading::class, 'electric_service_id')->latestOfMany('reading_date');
    }

    public function disconnections(): HasMany
    {
        return $this->hasMany(ElectricServiceDisconnection::class, 'electric_service_id')
            ->orderByDesc('disconnection_date')
            ->orderByDesc('id');
    }

    public function recalculateElectricConsumption(): void
    {
        $readings = $this->readings()->get();
        $prevImportedCalculated = 0.0;
        $prevProducedCalculated = 0.0;

        foreach ($readings as $reading) {
            if ($this->has_solar_power) {
                // For solar services: consumption = (imported_calculated Δ) - (produced_calculated Δ)
                $importedCalculated = (float) ($reading->imported_calculated ?? 0);
                $producedCalculated = (float) ($reading->produced_calculated ?? 0);

                $importedDelta = $importedCalculated - $prevImportedCalculated;
                $producedDelta = $producedCalculated - $prevProducedCalculated;

                $consumption = round($importedDelta - $producedDelta, 2);

                $prevImportedCalculated = $importedCalculated;
                $prevProducedCalculated = $producedCalculated;
            } else {
                // Non-solar services rely on the calculated imported reading difference.
                $importedCalculated = (float) ($reading->imported_calculated ?? 0);
                $consumption = round($importedCalculated - $prevImportedCalculated, 2);
                $prevImportedCalculated = $importedCalculated;
            }

            $reading->forceFill([
                'consumption_value' => $consumption,
            ])->saveQuietly();
        }
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (!$status || $status === 'all') {
            return $query;
        }

        if ($status === 'inactive') {
            return $query->where('is_active', false);
        }

        return $query->where('is_active', true);
    }

    public function scopeByCompany(Builder $query, $companyId): Builder
    {
        if (!$companyId) {
            return $query;
        }

        return $query->where('electricity_company_id', $companyId);
    }

    public function scopeByGovernorate(Builder $query, ?string $governorate): Builder
    {
        if (!$governorate) {
            return $query;
        }

        return $query->whereHas('building.site', function (Builder $builder) use ($governorate): void {
            $builder->where('governorate', $governorate);
        });
    }

    public function scopeWithSolar(Builder $query, $flag): Builder
    {
        if ($flag === null || $flag === '' || $flag === 'all') {
            return $query;
        }

        if (in_array($flag, ['1', 1, true, 'true', 'yes', 'with'], true)) {
            return $query->where('has_solar_power', true);
        }

        if (in_array($flag, ['0', 0, false, 'false', 'no', 'without'], true)) {
            return $query->where('has_solar_power', false);
        }

        return $query;
    }
}
