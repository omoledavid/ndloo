<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

enum OtpCodeTypes: string
{
    case VERIFICATION = 'verification';
    case LOGIN = 'login';
    case RESET = 'reset';
}
