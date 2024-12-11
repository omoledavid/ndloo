<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckBoosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-boosts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for expired boosts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
