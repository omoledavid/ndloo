<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum TransactionIcons: string
{
    case GIFT = 'https://api.ndloo.com/storage/icons/gift.png';
    case TOPUP = 'https://api.ndloo.com/storage/icons/topup.png';
    case SUBSCRIPTION = 'https://api.ndloo.com/storage/icons/subscription.png';
    case WITHDRAWAL = 'https://api.ndloo.com/storage/icons/withdrawal.png';
}
