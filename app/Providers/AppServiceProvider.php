<?php

namespace App\Providers;

use App\Contracts\Enums\SettingStates;
use App\Models\PersonalAccessToken;
use App\Support\Helpers\SmsSender;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsSender::class, function ($app) {
            $config = $app['config']->get('services');

            return new SmsSender(
                sid: SettingStates::TWILIO_SID->getValue(),
                auth_token: SettingStates::TWILIO_AUTH_TOKEN->getValue(),
                number: SettingStates::TWILIO_NUMBER->getValue(),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Password::defaults(function () {
            return app()->environment('production')
                ? Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : Password::min(8);
        });
    }
}
