<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum GiftStatus: int
{
    case DISABLE = 0;
    case ENABLE= 1;
}
