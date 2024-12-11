<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionCategory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
    ];

    public function plans(): HasMany
    {
        return $this->hasMany(SubscriptionPlan::class);
    }
}
