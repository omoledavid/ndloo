<?php

namespace App\Support\Managers;

use App\Support\Services\Payments\FlutterwaveMobileMoney;
use App\Support\Services\Payments\KorapayMobileMoney;
use Illuminate\Support\Manager;

class MobileMoneyManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return env('MOBILE_MONEY_DRIVER');
    }

    public function createFlutterwaveDriver(): FlutterwaveMobileMoney
    {
        return new FlutterwaveMobileMoney();
    }

    public function createKoraDriver(): KorapayMobileMoney
    {
        return new KorapayMobileMoney();
    }
}
