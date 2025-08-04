<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NdPlan extends Model
{
    protected $fillable = ['name', 'price', 'duration_days'];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(NdFeature::class, 'nd_plan_features', 'plan_id', 'feature_id')
                    ->withPivot('limit')
                    ->withTimestamps();
    }

    public function subscriptions()
    {
        return $this->hasMany(NdSubscription::class);
    }
}
