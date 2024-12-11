<?php

namespace App\Support\Services\Payments;

use App\Contracts\DataObjects\PaymentData;
use App\Contracts\Interfaces\MobileMoneyInterface;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class FlutterwaveMobileMoney extends BaseService implements MobileMoneyInterface
{
    public function initiate(object $request): JsonResponse
    {
        $chargeType = match ($request->currency) {
            'GHS' => 'mobile_money_ghana',
            'XAF' || 'XOF' => 'mobile_money_franco',
            'UGX' => 'mobile_money_uganda',
            'RWF' => 'mobile_money_rwanda',
            'ZMW' => 'mobile_money_zambia'
        };

        $url = "https://api.flutterwave.com/v3/charges?type=$chargeType";

        $req = Http::acceptJson()
            ->withToken(env('FLUTTERWAVE_CLIENT_SECRET'))
            ->post($url, $request->getFlutterwaveData());

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
                'authorization' => $response['meta']['authorization'],
            ]);
        }

        return $this->errorResponse(__('responses.unknownError'));
    }
}
