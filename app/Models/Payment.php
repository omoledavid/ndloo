<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'reference',
        'type',
        'channel',
        'currency',
        'status',
        'rate',
        'amount',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
