<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reference',
        'name',
        'user_id',
        'channel',
        'icon',
        'currency',
        'amount',
        'usdAmount',
    ];

    public function icon(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => asset($value)
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => date('jS M', strtotime($value)),

        );
    }
}
