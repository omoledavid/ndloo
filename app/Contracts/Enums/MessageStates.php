<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum MessageStates: int
{
    case UNREAD = 0;
    case READ = 1;
}
