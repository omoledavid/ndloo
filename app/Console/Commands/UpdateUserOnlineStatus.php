<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateUserOnlineStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-online-status';

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
        $threshold = now()->subMinutes(5);

        // Set offline all users
        User::query()->update(['is_online' => false]);

        // Set online users who are active in the last 5 mins
        User::query()
            ->where('last_seen_at', '>=', $threshold)
            ->update(['is_online' => true]);

        $this->info('User online statuses updated.');
    }
}
