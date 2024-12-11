<?php

namespace App\Jobs;

use App\Contracts\DataObjects\TransactionData;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private TransactionData $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly User $user,
        private readonly string $channel,
        private readonly string $icon,
        private readonly int $amount
    ) {
        $this->transaction = TransactionData::fromArray([
            'user_id' => $this->user,
            'channel' => $channel,
            'icon' => $icon,
            'amount' => $amount,
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Transaction::query()->create($this->transaction->toArray());
    }
}
