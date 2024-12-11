<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Services\CurrencyService;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function __construct(private readonly CurrencyService $currencyService) {}

    public function currencies(): JsonResponse
    {
        return $this->currencyService->currencies();
    }
}
