<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaterCompany extends Model
{
    use SoftDeletes;

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
