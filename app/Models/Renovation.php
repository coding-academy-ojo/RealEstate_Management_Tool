<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Renovation extends Model
{
  use SoftDeletes, LogsActivity;

  protected $fillable = [
    'innovatable_id',
    'innovatable_type',
    'date',
    'cost',
    'name',
    'description',
  ];

  protected $casts = [
    'date' => 'date',
    'cost' => 'decimal:2',
  ];

  /**
   * Get the parent innovatable model (Site, Land, or Building).
   */
  public function innovatable(): MorphTo
  {
    return $this->morphTo();
  }
}
