<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NdSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(NdPlan::class);
    }

    public function featureUsages()
    {
        return $this->hasMany(NdFeatureUsuage::class);
    }

    public function isActive(): bool
    {
        $now = now();
        
        // Check if subscription has started
        if ($now->lt($this->starts_at)) {
            return false;
        }
        
        // If ends_at is null, subscription is active (no end date)
        if ($this->ends_at === null) {
            return true;
        }
        
        // Check if subscription has ended
        return $now->lte($this->ends_at);
    }
}
