<?php

namespace App\Jobs;

use App\Contracts\DataObjects\TransactionData;
use App\Contracts\Enums\TransactionIcons;
use App\Contracts\Enums\TransactionTypes;
use App\Models\GiftPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserGift;
use App\Notifications\Gift\GiftFailureNotice;
use App\Notifications\Gift\GiftReceivedNotice;
use App\Notifications\Gift\GiftSentNotice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly GiftPlan $giftPlan, private readonly User $recipient) {}

    public function uniqId()
    {
        return auth()->user()->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (auth()->user()->wallet >= $this->giftPlan->amount) {
            $sender = User::find(auth()->user()->id);

            try {
                DB::beginTransaction();

                $sender->update([
                    'wallet' => $sender->wallet - $this->giftPlan->amount,
                ]);

                UserGift::create([
                    'user_id' => $this->recipient->id,
                    'sender_id' => $sender->id,
                    'gift_plan_id' => $this->giftPlan->id,
                ]);

                $this->recipient->update([
                    'credits' => $this->recipient->wallet + $this->giftPlan->amount,
                ]);

                //create sender transaction
                Transaction::create(TransactionData::fromArray([
                    'name' => TransactionTypes::GIFT_SENT->value,
                    'user_id' => $sender->id,
                    'amount' => $this->giftPlan->amount,
                    'icon' => TransactionIcons::GIFT->value,
                    'currency' => 'USD',
                    'usdAmount' => $this->giftPlan->amount,
                ])->toArray());

                //create recipient transaction
                Transaction::create(TransactionData::fromArray([
                    'name' => TransactionTypes::GIFT_RECEIVED->value,
                    'user_id' => $this->recipient->id,
                    'amount' => $this->giftPlan->amount,
                    'icon' => $this->giftPlan->icon,
                    'currency' => 'USD',
                    'usdAmount' => $this->giftPlan->amount,
                ])->toArray());

                DB::commit();

                //create notification
                try {
                    $sender->notify(new GiftSentNotice($this->recipient, $this->giftPlan->amount));
                    $this->recipient->notify(new GiftReceivedNotice($sender, $this->giftPlan->amount));
                } catch (\Throwable $th) {
                }

                //send push notification
            } catch (\Throwable $th) {

                Log::error($th);
                DB::rollBack();

                //failure notification to sender
                $sender->notify(new GiftFailureNotice($this->recipient, $this->giftPlan->amount));
            }
        }
    }
}
