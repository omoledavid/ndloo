<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'livestream_id',
        'gift_id',
        'quantity',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function livestream()
    {
        return $this->belongsTo(Livestream::class);
    }

    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }
}