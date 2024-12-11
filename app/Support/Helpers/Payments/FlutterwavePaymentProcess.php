<?php

namespace App\Support\Helpers\Payments;

use App\Jobs\Payments\FlutterwavePaymentJob;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class FlutterwavePaymentProcess extends ProcessWebhookJob
{
    public function handle()
    {
        $rawData = json_decode($this->webhookCall, true);
        $data = $rawData['payload'];

        $reference = $data['event'] === 'transfer.completed' ? $data['data']['reference'] : $data['data']['tx_ref'];

        FlutterwavePaymentJob::dispatchAfterResponse($reference);

        http_response_code(200);
    }
}
