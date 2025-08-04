<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NdFeatureUsuage extends Model
{
    protected $fillable = ['subscription_id', 'feature_id', 'used'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(NdSubscription::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(NdFeature::class);
    }
}
