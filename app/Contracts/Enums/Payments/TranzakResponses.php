<?php

declare(strict_types=1);

namespace App\Contracts\Enums\Payments;

enum TranzakResponses: string
{
    case RESPONSE_SUCCESS = 'SUCCESS';
    case RESPONSE_ERROR = 'ERROR';
    case PAYMENT_SUCCESS = 'SUCCESSFUL';
    case PAYMENT_ERROR = 'FAILED';
    case PAYMENT_PROCESSING = 'PENDING';
}
