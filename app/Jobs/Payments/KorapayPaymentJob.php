<?php

namespace App\Jobs\Payments;

use App\Contracts\Enums\Payments\KorapayResponses;
use App\Models\Payment;
use App\Models\User;
use App\Support\Helpers\Payments\PaymentHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class KorapayPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $url;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $reference
    ) {
        $this->url = "https://api.korapay.com/merchant/api/v1/charges/$reference";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payment = Payment::query()->where('reference', $this->reference)->first();

        if (is_null($payment)) {
            exit;
        }

        $request = Http::acceptJson()
            ->withToken(env('KORA_CLIENT_SECRET'))
            ->get($this->url);

        if ($request->successful()) {
            $response = $request->json();

            if ($response['status']) {
                $txData = PaymentHandler::generateTransactionData($payment, $response['data']);
                $user = User::find($payment->user_id);

                if ($response['data']['status'] === KorapayResponses::PAYMENT_SUCCESS->value) {
                    PaymentHandler::successfulPayment($user, $payment, $txData);
                } elseif ($response['data']['status'] === KorapayResponses::PAYMENT_ERROR->value) {
                    PaymentHandler::failedPayment($user, $payment, $txData);
                }
            } else {
                //transaction not found
                $payment->delete();
            }
        }
    }
}
