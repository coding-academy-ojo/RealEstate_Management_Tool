<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElectricServiceDisconnection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'electric_service_id',
        'disconnection_date',
        'reconnection_date',
        'reason',
    ];

    protected $casts = [
        'disconnection_date' => 'date',
        'reconnection_date' => 'date',
    ];

    public function electricityService(): BelongsTo
    {
        return $this->belongsTo(ElectricityService::class, 'electric_service_id');
    }
}
