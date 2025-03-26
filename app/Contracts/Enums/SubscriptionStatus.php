<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum SubscriptionStatus: int
{
    case DISABLE = 0;
    case ENABLE= 1;
}
