<?php

declare(strict_types=1);

namespace App\Support\Helpers;

use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SmsSender
{
    /**
     * Create a new SmsSender class instance.
     */
    public function __construct(
        private readonly string $sid,
        private readonly string $auth_token,
        private readonly string $number
    ) {}

    public function send(string $message, string $recipient): ?bool
    {
        $client = new Client($this->sid, $this->auth_token);

        try {
            $client->messages->create($recipient, ['from' => $this->number, 'body' => $message]);

            return true;
        } catch (TwilioException $th) {
            Log::error($th);

            return false;
        }
    }
}
