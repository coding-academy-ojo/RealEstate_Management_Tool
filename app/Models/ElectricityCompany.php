<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElectricityCompany extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'website',
    ];

    /**
     * Electricity services served by this company.
     */
    public function services(): HasMany
    {
        return $this->hasMany(ElectricityService::class);
    }
}
