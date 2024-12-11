<?php

declare(strict_types=1);

namespace App\Support\Services\Payments;

use App\Contracts\DataObjects\PaymentData;
use App\Contracts\Interfaces\MobileMoneyInterface;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class KorapayMobileMoney extends BaseService implements MobileMoneyInterface
{
    public function initiate(object $request): JsonResponse
    {

        $url = 'https://api.korapay.com/merchant/api/v1/charges/mobile-money';

        $req = Http::acceptJson()
            ->withToken(env('KORA_CLIENT_SECRET'))
            ->post($url, $request->getKoraData());

        if ($req->successful()) {
            $response = $req->json();

            return $this->successResponse(data: [
                'authorization' => $response['data']['auth_model'],
                'message' => $response['data']['message'],
                'reference' => $response['data']['transaction_reference'],
            ]);
        }

        return $this->errorResponse(__('responses.unknownError'));
    }

    public function authorize(object $request): JsonResponse
    {

        $url = 'https://api.korapay.com/merchant/api/v1/charges/mobile-money/authorize';

        $req = Http::acceptJson()
            ->withToken(env('KORA_CLIENT_SECRET'))
            ->post($url, $request->getKoraData());

        if ($req->successful()) {
            $response = $req->json();

            //store tx ref
            $paymentData = PaymentData::fromArray([
                'user_id' => $request->user()->id,
                'reference' => $request->reference,
                'channel' => 'flutterwave',
                'currency' => $request->currency,
                'rate' => ExchangeRate::where('currency', $request->currency)->first()->rate,
                'amount' => $response['data']['amount'],
            ]);

            Payment::create($paymentData->toArray());

            return $this->successResponse(data: [
                'authorization' => $response['data']['auth_model'],
                'message' => $response['data']['message'],
                'reference' => $response['data']['transaction_reference'],
            ]);
        }

        return $this->errorResponse(__('responses.unknownError'));
    }
}
