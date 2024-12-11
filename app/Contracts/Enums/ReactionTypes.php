<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum ReactionTypes: string
{
    case LIKE = 'like';
    case BLOCKED = 'block';
    case DISLIKED = 'dislike';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
