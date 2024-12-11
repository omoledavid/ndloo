<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyService extends BaseService
{
    public function currencies(): JsonResponse
    {
        return $this->successResponse(data: [
            'currencies' => Currency::all(),
        ]);
    }
}
