<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'sender',
        'recipient',
        'content',
        'media',
        'read',
    ];

    protected function casts(): array
    {
        return [
            'read' => 'bool',
        ];
    }

    public function media(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => is_null($value) ? [] : json_decode($value, true),
            set: fn (array $value) => json_encode($value)
        );
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient');
    }
}
