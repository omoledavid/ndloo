<?php

declare(strict_types=1);

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

class CardPayer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cardpayer';
    }
}
