<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum TransactionDuration: int
{
    case MONTHLY = 1;
    case QUARTERLY = 3;
    case BI_ANNUALLY = 6;
    case YEARLY = 12;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
