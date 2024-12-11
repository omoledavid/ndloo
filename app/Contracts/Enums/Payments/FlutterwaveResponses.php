<?php

declare(strict_types=1);

namespace App\Contracts\Enums\Payments;

enum FlutterwaveResponses: string
{
    case RESPONSE_SUCCESS = 'success';
    case RESPONSE_ERROR = 'error';
    case PAYMENT_SUCCESS = 'successful';
    case PAYMENT_ERROR = 'failed';
    case PAYMENT_PROCESSING = 'pending';
}
