<?php

namespace App\Console\Commands;

use App\Models\NdPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class SubscribeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subscribe-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    $users = User::all();
    $freePlan = NdPlan::where('price', 0)->first();

    if (!$freePlan) {
        $this->error('No free plan found.');
        return;
    }

    $subscriptionService = new SubscriptionService();

    foreach ($users as $user) {
        $activeSubscription = $user->activeSubscription()->first();
        if (!$activeSubscription) {
            $subscriptionService->subscribe($user, $freePlan);
            $this->info("Subscribed user {$user->id} to free plan.");
        }
    }
    }
}
