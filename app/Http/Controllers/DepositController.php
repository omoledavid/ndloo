<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Support\Services\DepositService;
use App\Support\Services\Payments\VerifyPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(private readonly DepositService $depositService) {}

    public function getRate(Request $request): JsonResponse
    {
        return $this->depositService->getRate($request);
    }

    public function generateInfo(PaymentRequest $request): JsonResponse
    {
        return $this->depositService->generateInfo($request);
    }

    public function getOptions(): JsonResponse
    {
        return $this->depositService->getOptions();
    }
    public function callback()
    {

    }

    public function verifyPayment(
        Payment $payment,
        Request $request,
        VerifyPaymentService $verifyPaymentService
    ): JsonResponse {
        return $verifyPaymentService->verify($payment, $request);
    }
}
