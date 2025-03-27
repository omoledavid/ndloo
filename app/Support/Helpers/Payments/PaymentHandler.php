<?php

namespace App\Support\Helpers\Payments;

use App\Contracts\DataObjects\TransactionData;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\Payments\TopupFailedNotice;
use App\Notifications\Payments\TopupSuccessNotice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentHandler
{
    private const CURRENCY = 'USD';

    public static function getUsdAmount(int|float $amount, string $currency, int|float $rate): int
    {
        Log::info('rate: '.$rate).' amount: '.$amount.' currency: '.$currency;
        $result = $currency === self::CURRENCY ? $amount : round($amount / $rate, 2, mode: PHP_ROUND_HALF_DOWN);
        Log::info('result: '.$result);
        return $result;
    }

    public static function generateTransactionData(Payment $payment, array $responseData): TransactionData
    {
        return TransactionData::fromArray([
            'name' => ucfirst($payment->type),
            'reference' => $payment->reference,
            'user_id' => $payment->user_id,
            'amount' => $responseData['amount'],
            'channel' => $payment->channel,
            'icon' => 'assets/img/' . $payment->channel . '.png',
            'currency' => $responseData['currencyCode'] ?? $responseData['currency'],
            'usdAmount' => self::getUsdAmount(
                $responseData['amount'],
                $responseData['currencyCode'] ?? $responseData['currency'],
                $payment->rate
            ),
        ]);
    }

    public static function successfulPayment(
        User            $user,
        Payment         $payment,
        TransactionData $txData
    )
    {
        //topup user wallet, delete payment and add transaction, and send notices
        try {
            DB::beginTransaction();

            $user->update(['wallet' => $user->wallet + floatval($txData->usdAmount)]);
            Transaction::create($txData->toArray());
            $payment->delete();

            DB::commit();

//            $user->notify(new TopupSuccessNotice($user, $txData->usdAmount));
            notify($user, 'TOPUP_SUCCESS',[
                'amount' => floatval($txData->usdAmount),
            ], ['email']);

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return false;
        }
    }

    public static function failedPayment(
        User            $user,
        Payment         $payment,
        TransactionData $txData
    ): ?bool
    {
        $payment->delete();
        notify($user, 'TOPUP_FAILED',[
            'amount' => floatval($txData->usdAmount),
        ], ['email']);
//        $user->notify(new TopupFailedNotice($user, $txData->usdAmount));

        return true;
    }
}
