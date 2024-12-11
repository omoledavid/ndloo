<?php

declare(strict_types=1);

namespace App\Support\Managers;

use App\Support\Services\Payments\FlutterwaveCardService;
use App\Support\Services\Payments\KoraCardService;
use Illuminate\Support\Manager;

class CardManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return env('CARD_PAYMENT_DRIVER');
    }

    public function createFlutterwaveDriver(): FlutterwaveCardService
    {
        return new FlutterwaveCardService();
    }

    public function createKoraDriver(): KoraCardService
    {
        return new KoraCardService();
    }
}
