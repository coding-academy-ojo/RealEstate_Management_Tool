<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaterCompany extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'website',
    ];

    /**
     * Water services associated with this company.
     */
    public function services(): HasMany
    {
        return $this->hasMany(WaterService::class);
    }
}
