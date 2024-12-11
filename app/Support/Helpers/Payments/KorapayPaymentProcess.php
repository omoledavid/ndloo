<?php

namespace App\Support\Helpers\Payments;

use App\Jobs\Payments\KorapayPaymentJob;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class KorapayPaymentProcess extends ProcessWebhookJob
{
    public function handle()
    {
        $rawData = json_decode($this->webhookCall, true);
        $data = $rawData['payload'];

        KorapayPaymentJob::dispatchAfterResponse($data['data']['reference']);

        //Acknowledge you received the response
        http_response_code(200);
    }
}
