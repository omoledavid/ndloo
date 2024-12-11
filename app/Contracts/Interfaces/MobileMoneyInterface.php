<?php

namespace App\Contracts\Interfaces;

use Illuminate\Http\JsonResponse;

interface MobileMoneyInterface
{
    public function initiate(object $request): JsonResponse;
}
