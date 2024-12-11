<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGift extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'sender_id',
        'gift_plan_id',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(GiftPlan::class, 'gift_plan_id');
    }
}
