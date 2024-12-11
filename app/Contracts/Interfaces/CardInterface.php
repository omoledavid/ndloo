<?php

declare(strict_types=1);

namespace App\Contracts\Interfaces;

use Illuminate\Http\JsonResponse;

interface CardInterface
{
    public function initiate(object $request): JsonResponse;

    public function authorize(object $request): JsonResponse;
}
