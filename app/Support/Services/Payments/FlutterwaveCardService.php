<?php

namespace App\Support\Services\Payments;

use App\Contracts\DataObjects\PaymentData;
use App\Contracts\Enums\Payments\FlutterwaveResponses;
use App\Contracts\Interfaces\CardInterface;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Support\Helpers\Payments\CardEncryption;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class FlutterwaveCardService extends BaseService implements CardInterface
{
    private string $paymentUrl = 'https://api.flutterwave.com/v3/charges?type=card';

    private string $validationUrl = 'https://api.flutterwave.com/v3/validate-charge';

    public function initiate(object $request): JsonResponse
    {
        $req = Http::acceptJson()
            ->withToken(env('FLUTTERWAVE_CLIENT_SECRET'))
            ->post($this->paymentUrl, [
                'client' => CardEncryption::flutterwaveEncrypt($request->getFlutterwaveData()),
            ]);

        if ($req->successful()) {
            $response = $req->json();

            //store tx ref
            $paymentData = PaymentData::fromArray([
                'user_id' => $request->user()->id,
                'reference' => $response['data']['tx_ref'],
                'channel' => 'flutterwave',
                'currency' => $response['data']['currency'],
                'rate' => ExchangeRate::where('currency', $response['data']['currency'])->first()->rate,
                'amount' => $response['data']['amount'],
            ]);

            Payment::create($paymentData->toArray());

            if ($response['data'] && $response['data']['status'] !== FlutterwaveResponses::PAYMENT_PROCESSING->value) {
                return $this->successResponse(data: [
                    'reference' => $response['data']['tx_ref'],
                ]);
            }

            return $this->successResponse(data: [
                'authorization' => $response['meta']['authorization'],
                'reference' => $response['data']['tx_ref'],
            ]);
        }
    }

    public function authorize(object $request): JsonResponse
    {
        $req = Http::acceptJson()
            ->withToken(env('FLUTTERWAVE_CLIENT_SECRET'))
            ->post($this->paymentUrl, [
                'client' => CardEncryption::flutterwaveEncrypt($request->getFlutterwaveData()),
            ]);

        if ($req->successful()) {
            $response = $req->json();

            return $this->successResponse(data: [
                'authorization' => $response['meta']['authorization'],
                'reference' => $response['data']['flw_ref'],
            ]);
        }
    }

    public function validate(object $request): JsonResponse
    {
        $req = Http::acceptJson()
            ->withToken(env('FLUTTERWAVE_CLIENT_SECRET'))
            ->post($this->validationUrl, [
                'otp' => $request->otp,
                'flw_ref' => $request->reference,
            ]);

        if ($req->successful()) {
            $response = $req->json();

            return $this->successResponse(data: [
                'reference' => $response['data']['tx_ref'],
            ]);
        }
    }
}
