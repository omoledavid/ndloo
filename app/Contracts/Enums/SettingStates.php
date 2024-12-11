<?php

declare(strict_types=1);

namespace App\Contracts\Enums;

use App\Models\Setting;

enum SettingStates: string
{
    case AGORA_APP_ID = 'agora-app-id';
    case AGORA_APP_CERTIFICATE = 'agora-app-certificate';
    case TWILIO_SID = 'twilio-sid';
    case TWILIO_AUTH_TOKEN = 'twilio-auth-token';
    case TWILIO_NUMBER = 'twilio-number';
    case TRANZAK_APP_ID = 'tranzak-app-id';
    case TRANZAK_API_KEY = 'tranzak-api-key';
    case TRANZAK_API_URL = 'tranzak-api-url';
    case TRANZAK_RETURN_URL = 'tranzak-return-url';
    case TRANZAK_WEBHOOK_AUTH_KEY = 'tranzak-webhook-auth-key';

    public function getValue(): float|int|string
    {
        return match ($this) {
            self::AGORA_APP_ID => $this->getSettingValue($this->value),
            self::AGORA_APP_CERTIFICATE => $this->getSettingValue($this->value),
            self::TWILIO_SID => $this->getSettingValue($this->value),
            self::TWILIO_AUTH_TOKEN => $this->getSettingValue($this->value),
            self::TWILIO_NUMBER => $this->getSettingValue($this->value),
            self::TRANZAK_APP_ID => $this->getSettingValue($this->value),
            self::TRANZAK_API_KEY => $this->getSettingValue($this->value),
            self::TRANZAK_API_URL => $this->getSettingValue($this->value),
            self::TRANZAK_RETURN_URL => $this->getSettingValue($this->value),
            self::TRANZAK_WEBHOOK_AUTH_KEY => $this->getSettingValue($this->value)
        };
    }

    private function getSettingValue($item): int|float|string
    {
        return Setting::query()
            ->where('item', $item)
            ->first()
            ->value;
    }
}
