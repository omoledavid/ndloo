<?php

namespace App\Console\Commands;

use App\Contracts\Enums\SettingStates;
use App\Models\User;
use App\Support\Helpers\RtcTokenBuilder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateCallToken extends Command
{
    private const PUBLISHER = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-call-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an hourly audio/video call token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::query()
            ->lazyById()
            ->each(function (object $user) {
                $user->update([
                    'token' => getStreamToken($user)
                ]);
            });
//        User::query()
//            ->lazyById()
//            ->each(function (object $user) {
//                $user->update([
//                    'token' => RtcTokenBuilder::buildTokenWithUid(
//                        SettingStates::AGORA_APP_ID->getValue(),
//                        SettingStates::AGORA_APP_CERTIFICATE->getValue(),
//                        $user->id,
//                        0,
//                        self::PUBLISHER,
//                        Carbon::now()->addHours(24)->timestamp
//                    ),
//                ]);
//            });

    }
}
