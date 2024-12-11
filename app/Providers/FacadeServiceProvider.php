<?php

namespace App\Providers;

use App\Support\Managers\CardManager;
use App\Support\Managers\MobileMoneyManager;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('cardpayer', function ($app) {
            return new CardManager($app);
        });

        $this->app->singleton('mobilemoneypayer', function ($app) {
            return new MobileMoneyManager($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
