<?php

namespace App\Support\Helpers\Payments;

use App\Contracts\DataObjects\TransactionData;
use App\Contracts\Enums\TransactionIcons;
use App\Contracts\Enums\TransactionTypes;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\Payment\WithdrawalFailedNotice;
use App\Notifications\Payment\WithdrawalSuccessNotice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferHandler
{
    private const CURRENCY = 'USD';

    public function getUsdAmount(int|float $amount, string $currency, int|float $rate): int
    {
        return $currency === self::CURRENCY ? $amount : round($amount / $rate, mode: PHP_ROUND_HALF_DOWN);
    }

    public static function generateTransactionData(Payment $payment, array $responseData): TransactionData
    {
        return TransactionData::fromArray([
            'name' => TransactionTypes::WITHDRAWAL->value,
            'reference' => $responseData['reference'],
            'user_id' => $payment->user_id,
            'amount' => $responseData['amount'],
            'channel' => $payment->channel,
            'icon' => TransactionIcons::WITHDRAWAL->value,
            'currency' => $responseData['currency'],
            'usdAmount' => self::getUsdAmount(
                $responseData['amount'],
                $responseData['currency'],
                $payment->rate
            ),
        ]);
    }

    public static function successfulPayment(
        User $user,
        Payment $payment,
        TransactionData $txData
    ): ?bool {
        //delete payment and add transaction, and send notices
        try {
            DB::beginTransaction();

            Transaction::create($txData->toArray());
            $payment->delete();

            DB::commit();

            $user->notify(new WithdrawalSuccessNotice($user, $txData->usdAmount));

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return false;
        }
    }

    public static function failedPayment(
        User $user,
        Payment $payment,
        TransactionData $txData
    ): ?bool {
        //refund users wallet
        try {
            DB::beginTransaction();
            $user->update(['wallet' => $user->wallet + $txData->amount]);
            $payment->delete();
            DB::commit();

            $user->notify(new WithdrawalFailedNotice($user, $txData->usdAmount));

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info($th);

            return false;
        }
    }
}
