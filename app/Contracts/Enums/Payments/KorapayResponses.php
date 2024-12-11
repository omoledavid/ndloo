<?php

declare(strict_types=1);

namespace App\Contracts\Enums\Payments;

enum KorapayResponses: string
{
    case RESPONSE_SUCCESS = true;
    case RESPONSE_ERROR = false;
    case PAYMENT_SUCCESS = 'success';
    case PAYMENT_ERROR = 'failed';
    case PAYMENT_PROCESSING = 'processing';
}
