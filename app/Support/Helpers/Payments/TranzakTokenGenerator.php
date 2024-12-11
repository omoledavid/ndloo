<?php

namespace App\Support\Helpers\Payments;

use App\Contracts\Enums\SettingStates;
use Illuminate\Support\Facades\Http;

class TranzakTokenGenerator
{
    public static function generateToken(): ?string
    {
        $request1 = Http::acceptJson()
            ->withHeaders([
                'X-App-ID' => SettingStates::TRANZAK_APP_ID->getValue(),
            ])
            ->post(SettingStates::TRANZAK_API_URL->getValue().'/auth/token', [
                'appId' => SettingStates::TRANZAK_APP_ID->getValue(),
                'appKey' => SettingStates::TRANZAK_API_KEY->getValue(),
            ]);
            
       if ($request1->successful() && $request1->json()['success']) {
            return $request1->json()['data']['token'];
        }

        return null;
    }
}
