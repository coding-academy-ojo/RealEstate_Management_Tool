<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectricReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'electric_service_id',
        'imported_current',
        'imported_calculated',
        'produced_current',
        'produced_calculated',
        'saved_energy',
        'consumption_value',
        'bill_amount',
        'is_paid',
        'reading_date',
        'meter_image',
        'bill_image',
        'notes',
    ];

    protected $casts = [
        'imported_current' => 'decimal:2',
        'imported_calculated' => 'decimal:2',
        'produced_current' => 'decimal:2',
        'produced_calculated' => 'decimal:2',
        'saved_energy' => 'decimal:2',
        'consumption_value' => 'decimal:2',
        'bill_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'reading_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (ElectricReading $reading): void {
            $reading->refreshServiceConsumption();
        });

        static::deleted(function (ElectricReading $reading): void {
            $reading->refreshServiceConsumption();
        });
    }

    public function electricityService(): BelongsTo
    {
        return $this->belongsTo(ElectricityService::class, 'electric_service_id');
    }

    private function refreshServiceConsumption(): void
    {
        $service = $this->electricityService ?? ElectricityService::find($this->electric_service_id);

        if ($service) {
            $service->recalculateElectricConsumption();
        }
    }
}
