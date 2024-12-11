<?php

namespace App\Jobs\Payments;

use App\Contracts\Enums\Payments\FlutterwaveResponses;
use App\Models\Payment;
use App\Models\User;
use App\Support\Helpers\Payments\PaymentHandler;
use App\Support\Helpers\Payments\TransferHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwavePaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $url;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $reference
    ) {
        $this->url = "https://api.flutterwave.com/v3/transactions/$reference/verify";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payment = Payment::query()->where('reference', $this->reference)->first();

        Log::info($payment);

        if (is_null($payment)) {
            Log::info('Oops, No payment found');
            exit;
        }

        $request = Http::acceptJson()
            ->withToken(env('FLUTTERWAVE_CLIENT_SECRET'))
            ->get($this->url);

        if ($request->successful()) {
            Log::info('Request successful');

            $response = $request->json();
            $user = User::find($payment->user_id);

            if ($response['event'] === 'transfer.completed') {
                //handle transfer events
                $txData = TransferHandler::generateTransactionData($payment, $response['data']);

                if (strtolower($response['data']['status']) === FlutterwaveResponses::PAYMENT_SUCCESS->value) {
                    Log::info('Transfer success');
                    TransferHandler::successfulPayment($user, $payment, $txData);
                } elseif (strtolower($response['data']['status']) === FlutterwaveResponses::PAYMENT_ERROR->value) {
                    Log::info('Transfer failed');
                    TransferHandler::failedPayment($user, $payment, $txData);
                }
            } else {
                $txData = PaymentHandler::generateTransactionData($payment, $response['data']);

                if ($response['data']['status'] === FlutterwaveResponses::PAYMENT_SUCCESS->value) {
                    Log::info('Payment success');
                    PaymentHandler::successfulPayment($user, $payment, $txData);
                } elseif ($response['data']['status'] === FlutterwaveResponses::PAYMENT_ERROR->value) {
                    Log::info('Payment failed');
                    PaymentHandler::failedPayment($user, $payment, $txData);
                }
            }
        }
    }
}
