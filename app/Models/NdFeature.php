<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NdFeature extends Model
{
    protected $fillable = ['name', 'label'];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(NdPlan::class)
                    ->withPivot('limit')
                    ->withTimestamps();
    }
}
