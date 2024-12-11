<?php

namespace App\Http\Controllers;

use App\Support\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentController extends Controller
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

        if ($verification['status']) {
            // Payment was successful
            return response()->json(['status' => 'success', 'data' => $verification['data']]);
        }

        return response()->json(['status' => 'error', 'message' => $verification['message']]);
    }
}

