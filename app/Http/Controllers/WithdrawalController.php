<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalRequest;
use App\Support\Services\WithdrawalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(private readonly WithdrawalService $withdrawalService) {}

    public function countries(): JsonResponse
    {
        return $this->withdrawalService->countries();
    }

    public function withdraw(WithdrawalRequest $request): JsonResponse
    {
        return $this->withdrawalService->withdraw($request);
    }

    public function verify(Request $request): JsonResponse
    {
        return $this->withdrawalService->verify($request);
    }
}
