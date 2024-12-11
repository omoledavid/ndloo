<?php

namespace App\Support\Services\Payments;

use App\Contracts\DataObjects\PaymentData;
use App\Contracts\Enums\Payments\KorapayResponses;
use App\Contracts\Interfaces\CardInterface;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Support\Helpers\Payments\CardEncryption;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class KoraCardService extends BaseService implements CardInterface
{
    private string $paymentUrl = 'https://api.korapay.com/merchant/api/v1/charges/card';

    private string $authorizationUrl = 'https://api.korapay.com/merchant/api/v1/charges/card/authorize';

    public function initiate(object $request): JsonResponse
    {
        $req = Http::acceptJson()
            ->withToken(env('KORA_CLIENT_SECRET'))
            ->post($this->paymentUrl, [
                'charge_data' => CardEncryption::koraEncrypt($request->getKorapayData()),
            ]);

        if ($req->successful()) {
            $response = $req->json();

            //store tx ref
            $paymentData = PaymentData::fromArray([
                'user_id' => $request->user()->id,
                'reference' => $response['data']['payment_reference'],
                'channel' => 'kora',
                'currency' => $response['data']['currency'],
                'rate' => ExchangeRate::where('currency', $response['data']['currency'])->first()->rate,
                'amount' => $response['data']['amount'],
            ]);

            Payment::create($paymentData->toArray());

            if ($response['data']['status'] === KorapayResponses::PAYMENT_PROCESSING->value) {
                return $this->successResponse(data: [
                    'authorization' => $response['data']['authorization'],
                    'reference' => $response['data']['transaction_reference'],
                ]);
            }

            return $this->successResponse(__('responses.paymentSuccessful'), [
                'reference' => $response['data']['payment_reference'],
            ]);
        }
    }

    public function authorize(object $request): JsonResponse
    {
        $req = Http::acceptJson()
            ->withToken(env('KORA_CLIENT_SECRET'))
            ->post($this->authorizationUrl, [
                'transaction_reference' => $request->reference,
                'authorization' => $request->authorization,
            ]);

        if ($req->successful()) {
            $response = $req->json();

            if ($response['data']['status'] === KorapayResponses::PAYMENT_PROCESSING->value) {
                return $this->successResponse(data: [
                    'authorization' => $response['data']['authorization'],
                    'reference' => $response['data']['transaction_reference'],
                ]);
            }

            return $this->successResponse(__('responses.paymentSuccessful'), [
                'reference' => $response['data']['payment_reference'],
            ]);
        }
    }
}
