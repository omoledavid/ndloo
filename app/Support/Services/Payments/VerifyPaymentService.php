<?php

namespace App\Support\Services\Payments;

use App\Contracts\Enums\Payments\TranzakResponses;
use App\Contracts\Enums\SettingStates;
use App\Models\Payment;
use App\Models\User;
use App\Support\Helpers\Payments\PaymentHandler;
use App\Support\Helpers\Payments\TranzakTokenGenerator;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerifyPaymentService extends BaseService
{
    public function verify(Payment $payment, Request $request): JsonResponse
    {
        $ref = $payment->reference;

        if (is_null($payment)) {
            return $this->errorResponse(__('responses.invalidPayment'));
        }

        $token = TranzakTokenGenerator::generateToken();
        $requestId = $request->query('requestId');

        if (is_null($token)) {
            return $this->errorResponse(__('responses.unknownError'));
        }

        $req = Http::acceptJson()
            ->withToken($token)
            ->withHeaders([
                'X-App-ID' => SettingStates::TRANZAK_APP_ID->getValue(),
            ])
            ->get(SettingStates::TRANZAK_API_URL->getValue()."/xp021/v1/request/details?requestId=$requestId");

        if ($req->successful()) {
            $response = $req->json();
            $transactionPresent = $response['success'];

            if ($transactionPresent) {
                $txData = PaymentHandler::generateTransactionData($payment, $response['data']);
                $user = User::find($payment->user_id);

                $transactionSuccessful = $response['data']['status'] === TranzakResponses::PAYMENT_SUCCESS->value;
                $transactionFailed = $response['data']['status'] === TranzakResponses::PAYMENT_ERROR->value;

                if ($transactionSuccessful) {
                    $handled = PaymentHandler::successfulPayment($user, $payment, $txData);

                    return $handled
                        ? $this->successResponse(__('responses.paymentSuccessful'))
                        : $this->errorResponse(_('responses.unknownError'));
                }

                if ($transactionFailed) {
                    $handled = PaymentHandler::failedPayment($user, $payment, $txData);

                    return $handled
                        ? $this->successResponse(__('responses.paymentFailed'))
                        : $this->errorResponse(_('responses.unknownError'));
                }

                return $this->errorResponse(__('responses.pendingConfirmation'));
            }

            //transaction not found
            $payment->delete();

            return $this->errorResponse(__('responses.invalidPayment'));
        }

        //request failed
        return $this->errorResponse(__('responses.unknownError'));
    }
}
