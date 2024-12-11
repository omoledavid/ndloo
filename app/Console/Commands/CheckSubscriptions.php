<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for expired subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
