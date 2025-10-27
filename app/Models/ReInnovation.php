<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReInnovation extends Model
{
    use SoftDeletes;

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

    /*========= Relations =========*/

    /**
     * Get the parent innovatable model (Site, Land, or Building).
     */
    public function innovatable(): MorphTo
    {
        return $this->morphTo();
    }
}
