<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'desc',
        'price',
        'chat',
        'call',
        'hide_profile',
        'save_messages',
        'period',
        'subscription_category_id',
    ];

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $value / 100,
            set: fn (int $value) => $value * 100
        );
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('active');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SubscriptionCategory::class, 'subscription_category_id');
    }
}
