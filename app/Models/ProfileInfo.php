<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProfileInfo extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category',
        'name',
        'type',
        'options',
    ];

    public function options(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => is_null($value) ? [] : json_decode($value, true),
            set: fn (array $value) => json_encode($value)
        );
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('content');
    }
}
