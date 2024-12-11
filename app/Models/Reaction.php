<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'actor',
        'recipient',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient');
    }
}
