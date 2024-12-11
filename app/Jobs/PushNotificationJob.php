<?php

namespace App\Jobs;

use App\Contracts\DataObjects\NotificationData;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly User $user,
        private readonly NotificationData $notificationData
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->user->pushNotice) {
            $this->user->load('pushtokens');
            
            foreach ($this->user->pushtokens as $pushToken) {
                $response = Http::acceptJson()
                    ->post('https://exp.host/--/api/v2/push/send?useFcmV1=true', [
                        'to' => $pushToken->token,
                        'title' => $this->notificationData->title,
                        'body' => $this->notificationData->body,
                        //'data' => json_encode($this->notificationData->data),
                    ]);
                    
                    
            }
        }
    }
}
