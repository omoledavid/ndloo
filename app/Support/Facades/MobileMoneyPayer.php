<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

class MobileMoneyPayer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mobilemoneypayer';
    }
}
