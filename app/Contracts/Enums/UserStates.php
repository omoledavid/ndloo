<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum UserStates: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case SUSPENDED = 2;
}
