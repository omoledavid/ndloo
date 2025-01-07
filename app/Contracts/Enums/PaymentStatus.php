<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
}
