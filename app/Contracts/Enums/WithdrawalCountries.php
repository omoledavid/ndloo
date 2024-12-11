<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum WithdrawalCountries: string
{
    case EG = 'EG';
    case ET = 'ET';
    case GH = 'GH';
    case KE = 'KE';
    case MW = 'MW';
    case NG = 'NG';
    case RW = 'RW';
    case SL = 'SL';
    case TZ = 'TZ';
    case UG = 'UG';
    case US = 'US';
    case ZA = 'ZA';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
