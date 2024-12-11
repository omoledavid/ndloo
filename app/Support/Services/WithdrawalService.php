<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\Enums\SettingStates;
use App\Contracts\Enums\WithdrawalCountries;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Models\Transaction;
use App\Support\Helpers\Payments\PaymentHandler;
use App\Support\Helpers\Payments\TranzakTokenGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Stichoza\GoogleTranslate\GoogleTranslate;

class WithdrawalService extends BaseService
{
    public function countries(): JsonResponse
    {
        return $this->successResponse(data: [
            'countries' => WithdrawalCountries::values(),
        ]);
    }

    public function withdraw(object $request): JsonResponse
    {
        $totalCharge = $request->amount + (0.1 * $request->amount);
        $rate = ExchangeRate::where('currency', $request->currency)->first();

        if (is_null($rate)) {
            return $this->errorResponse(__('responses.invalidCurrency'));
        }

        $usdTotalCharge = $totalCharge / $rate->withdrawal_rate;

        if ($usdTotalCharge > $request->user()->wallet) {
            return $this->errorResponse(__('responses.insufficientFunds'));
        }

        $reference = 'Tx_'.Uuid::uuid4();

        $paymentData = [
            'type' => 'withdrawal',
            'user_id' => $request->user()->id,
            'reference' => $reference,
            'channel' => env('DEFAULT_PAYMENT_CHANNEL'),
            'currency' => $request->currency,
            'rate' => $rate->withdrawal_rate,
            'amount' => $request->amount,
        ];

        try {
            DB::beginTransaction();
            $payment = Payment::create($paymentData);

            $request->user()->update([
                'wallet' => $request->user()->wallet - $usdTotalCharge,
            ]);

            $token = TranzakTokenGenerator::generateToken();

            if (is_null($token)) {
                return $this->errorResponse(__('responses.unknownError'));
            }

            $request1 = Http::acceptJson()
                ->withToken($token)
                ->withHeaders([
                    'X-App-ID' => SettingStates::TRANZAK_APP_ID->getValue(),
                ])
                ->post(SettingStates::TRANZAK_API_URL->getValue().'/xp021/v1/transfer/to-mobile-wallet', [
                    'amount' => $request->amount,
                    'currencyCode' => $request->currency,
                    'description' => 'Wallet Deposit',
                    'feeIsPaidByPayee' => true,
                    'customTransactionRef' => $reference,
                    'payeeAccountId' => $request->phone,
                    'payeeAccountName' => $request->account_name,
                    'payeeNote' => 'Wallet Withdrawal',
                ]);

            if ($request1->successful()) {

                try {
                    $payment->update([
                        'reference' => $request1->json()['data']['transferId'],
                    ]);

                    DB::commit();

                    return $this->successResponse(__('responses.transferProcessing'), [
                        'reference' => $request1->json()['data']['transferId'],
                    ]);
                } catch (\Throwable $th) {
                    return $this->errorResponse(GoogleTranslate::trans($request1->json()['errorMsg'], App::getLocale(), 'en'));
                }
            }

            return $this->errorResponse(__('responses.unknownError'));

        } catch (\Throwable $th) {
            DB::rollback();

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    public function verify(object $request): JsonResponse
    {

        $reference = $request->query('reference');
        $token = TranzakTokenGenerator::generateToken();

        if (is_null($token)) {
            return $this->errorResponse(__('responses.unknownError'));
        }

        $request1 = Http::acceptJson()
            ->withToken($token)
            ->withHeaders([
                'X-App-ID' => SettingStates::TRANZAK_APP_ID->getValue(),
            ])
            ->get(SettingStates::TRANZAK_API_URL->getValue()."/xp021/v1/transfer/details?transferId=$reference");


        if ($request1->successful() && $request1->json('success')) {
            $status = strtolower($request1->json()['data']['status']);

            $payment = Payment::where('reference', $reference)->first();

            if ($status === 'successful') {
                $txData = PaymentHandler::generateTransactionData($payment, $request1->json()['data']);
                Transaction::create($txData->toArray());
                $payment->delete();

                return $this->successResponse(__('responses.withdrawalSuccessful'));
            }

            if ($status === 'processing') {
                return $this->successResponse(__('responses.withdrawalPending'));
            }

            if ($status === 'failed') {
                //refund user
                $rate = ExchangeRate::where('currency', $payment->currency)->first();
                $usdTotalCharge = $payment->amount / $rate->withdrawal_rate;

                try {
                    $request->user()->update([
                        'wallet' => $request->user()->wallet + $usdTotalCharge,
                    ]);
                    $payment->delete();

                    return $this->errorResponse(__('responses.withdrawalFailed'));
                } catch (\Throwable $th) {
                    return $this->errorResponse(__('responses.unknownError'));
                }

            }
        }

        return $this->errorResponse(__('responses.unknownError'));

    }
}
