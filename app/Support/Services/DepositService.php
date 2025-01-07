<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\Enums\PaymentStatus;
use App\Contracts\Enums\SettingStates;
use App\Http\Requests\PaymentRequest;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Models\PaymentOption;
use App\Support\Helpers\Payments\TranzakTokenGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;

class DepositService extends BaseService
{
    public function getRate(Request $request): JsonResponse
    {
        $exchangeRate = ExchangeRate::where('currency', $request->query('currency'))->first();

        return is_null($exchangeRate)
            ? $this->errorResponse(__('responses.invalidCurrency'))
            : $this->successResponse(data: [
                'rate' => $exchangeRate,
            ]);
    }
    public function getOptions(): JsonResponse
    {
        return $this->successResponse(data: [
            'options' => PaymentOption::all(),
        ]);
    }
    public function generateInfo(PaymentRequest $request): JsonResponse
    {
        // Generate a unique reference for the payment
        $reference = 'Tx_'.Uuid::uuid4();
        $rate = ExchangeRate::where('currency', $request->currency)->first();

        if (is_null($rate)) {
            return $this->errorResponse(__('responses.invalidCurrency'));
        }

        // Determine the payment channel (from .env or provided)
        $channel = $request->channel ? $request->channel : env('DEFAULT_PAYMENT_CHANNEL', 'tranzak'); // Default to 'paystack'


        // Set up the payment data to store or use later
        $paymentData = [
            'user_id' => $request->user()->id,
            'type' => 'deposit',
            'reference' => $reference,
            'channel' => $channel,
            'status' => PaymentStatus::PENDING,
            'currency' => $request->currency,
            'rate' => $rate->deposit_rate,
            'amount' => $request->amount,
        ];

        // Handle Paystack payment
        if ($channel === 'paystack') {
            $user = $request->user();
            $paymentUrl = $this->initializePaystackPayment($request->amount, $request->currency, $reference, $user->email);


            if ($paymentUrl) {
                // Return the payment URL for the client to complete the payment
                $payment = Payment::create($paymentData);
                if(!$payment)
                {
                    return $this->errorResponse(__('responses.paymentFailed'));
                }
                return $this->successResponse(data: [
                    'payment_url' => $paymentUrl,
                    'reference' => $reference,
                    'payment' => $payment
                ]);
            } else {
                return $this->errorResponse(__('responses.paystackInitializationFailed'));
            }
        }elseif ($channel === 'tranzak'){
            $token = TranzakTokenGenerator::generateToken();
            $url = SettingStates::TRANZAK_API_URL->getValue().'/xp021/v1/request/create';


            $data = [
                'amount' => $request->amount,
                'currencyCode' => $request->currency,
                'description' => 'Wallet Deposit',
                'payerNote' => 'Wallet Deposit',
                'customization' => [
                    'title' => 'Ndloo Technologies',
                    'logoUrl' => 'https://api.ndloo.com/assets/img/logo.png'
                ],
                'mchTransactionRef' => $reference,
                'returnUrl' => SettingStates::TRANZAK_RETURN_URL->getValue(),
            ];

            $headers = ['X-App-ID' => SettingStates::TRANZAK_APP_ID->getValue()];

            if (is_null($token)) {
                return $this->errorResponse(__('responses.unknownError'));
            }

            try {

                $payment = Payment::create($paymentData);


                $request1 = Http::acceptJson()
                    ->withToken($token)
                    ->withHeaders($headers)
                    ->post($url, $data);

                if ($request1->successful()) {
                    $status = $request1->json('success');


                    if (! $status) {
                        return $this->errorResponse(__('responses.unknownError'));
                    }


                    $respUrl = $request1->json()['data']['links']['paymentAuthUrl'];

                    $payment->update([
                        'reference' => $request1->json()['data']['requestId'],
                    ]);

                    return $this->successResponse(data: [
                        'payment' => $payment,
                        'url' => $respUrl,
                        'id' => $request1->json()['data']['requestId'],
                    ]);
                }

                return $this->errorResponse(__('responses.unknownError'));
            } catch (\Throwable $th) {
                Log::error($th);

                return $this->errorResponse(__('responses.unknownError'));
            }
        }


        return $this->successResponse(data: $paymentData);
    }

    // Initialize Paystack Payment
    private function initializePaystackPayment(float $amount, string $currency, string $reference, string $email)
    {
        try {
            // Initialize a Guzzle client
            $client = new Client();



            // Make the API request to Paystack's transaction/initialize endpoint
            $response = $client->post('https://api.paystack.co/transaction/initialize', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email, // Replace with actual customer email
                    'amount' => $amount * 100,  // Paystack expects the amount in kobo
                    'reference' => $reference,
                    'currency' => $currency,
                    'callback_url' => route('paystack.callback', ['reference' => $reference]), // Paystack callback URL
                ],
            ]);


            $responseBody = json_decode($response->getBody()->getContents(), true);


            if ($responseBody['status'] === true) {
                return $responseBody['data']['authorization_url']; // Return URL for user to complete payment
            }

            return $responseBody; // If initialization failed
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Paystack Payment Initialization Error: ' . $e->getMessage());
            return null;
        }
    }
}
