<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum TransactionTypes: string
{
    case DEPOSIT = 'Deposit';
    case GIFT_SENT = 'Gift sent';
    case GIFT_RECEIVED = 'Gift Received';
    case GIFT_SOLD = 'Gift Sold';
    case WITHDRAWAL = 'Withdrawal';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
