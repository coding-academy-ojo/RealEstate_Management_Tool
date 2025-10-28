<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterReading extends Model
{
  use HasFactory;

  protected $fillable = [
    'water_service_id',
    'previous_reading',
    'current_reading',
    'consumption_value',
    'bill_amount',
    'is_paid',
    'reading_date',
    'meter_image',
    'bill_image',
    'notes',
  ];

  protected $casts = [
    'previous_reading' => 'decimal:2',
    'current_reading' => 'decimal:2',
    'consumption_value' => 'decimal:2',
    'bill_amount' => 'decimal:2',
    'is_paid' => 'boolean',
    'reading_date' => 'date',
  ];

  public function waterService(): BelongsTo
  {
    return $this->belongsTo(WaterService::class);
  }
}
