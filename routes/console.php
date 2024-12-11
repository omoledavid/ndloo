<?php

use App\Console\Commands\CheckBoosts;
use App\Console\Commands\CheckPayments;
use App\Console\Commands\CheckSubscriptions;
use App\Console\Commands\GenerateCallToken;
use Illuminate\Support\Facades\Schedule;
use Spatie\WebhookClient\Models\WebhookCall;

Schedule::command(CheckBoosts::class)->daily();
Schedule::command(CheckSubscriptions::class)->daily();
Schedule::command(CheckPayments::class)->hourly();
Schedule::command(GenerateCallToken::class)->hourly();

Schedule::command('model:prune', [
    '--model' => [WebhookCall::class],
])->daily();
