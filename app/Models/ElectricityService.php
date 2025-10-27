<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectricityService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'building_id',
        'company_name',
        'registration_number',
        'previous_reading',
        'current_reading',
        'reading_date',
        'reset_file',
        'remarks',
    ];

    protected $casts = [
        'previous_reading' => 'decimal:2',
        'current_reading'  => 'decimal:2',
        'reading_date'     => 'date',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
