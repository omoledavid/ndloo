<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOption extends Model
{
    use HasFactory, HasUuids;
    protected $primaryKey = 'name';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'logo',
    ];

    public function logo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => asset($value)
        );
    }
}
