<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class WaterReading extends Model
{
  use HasFactory, LogsActivity;

  protected $fillable = [
    'water_service_id',
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

  /**
   * Get the name to use in activity descriptions
   */
  public function getActivityName(): string
  {
    // Show reading date if available, otherwise just "Water Reading"
    if ($this->reading_date) {
      return "Water Reading ({$this->reading_date->format('M d, Y')})";
    }
    return "Water Reading";
  }
}
