<?php

namespace App\Http\Controllers;

use App\Contracts\Enums\PaymentStatus;
use App\Models\Payment;
use App\Support\Helpers\Payments\PaymentHandler;
use App\Support\Services\BaseService;
use App\Support\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends BaseService
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return response()->json(['status' => 'error', 'message' => 'No reference found']);
        }

        // Verify the payment using the reference
        $verification = $this->paystackService->verifyPayment($reference);
        $verification['data']['amount'] = $verification['data']['amount'] / 100;

        if ($verification['data']['status'] == 'success') {
            // Payment was successful
            $payment = Payment::where('reference', $reference)->first();
            $payment->status = PaymentStatus::PENDING;
            $payment->save();
            $user = $payment->user;
            $txData = PaymentHandler::generateTransactionData($payment, $verification['data']);
            $handled = PaymentHandler::successfulPayment($user, $payment, $txData);

            return $handled
                ? redirect()->away($payment->callback_url ?? 'https://ndloo.com/dashboard/wallet')
                : $this->errorResponse(_('responses.unknownError'));
        }

        return response()->json(['status' => 'error', 'message' => $verification['message']]);
    }
}

