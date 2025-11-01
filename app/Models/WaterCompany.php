<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class WaterCompany extends Model
{
    use SoftDeletes, LogsActivity;

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
