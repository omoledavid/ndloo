<?php

declare(strict_types=1);

namespace App\Contracts\DataObjects;

class PaymentData extends BaseData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly string $user_id,
        private readonly string $reference,
        private readonly string $channel,
        private readonly string $currency,
        private readonly int $rate,
        private readonly float|int $amount
    ) {}

    public static function fromArray(array $data): self
    {
        return new static(
            user_id: $data['user_id'],
            reference: $data['reference'],
            channel: $data['channel'],
            currency: $data['currency'],
            rate: $data['rate'],
            amount: $data['amount']
        );
    }
}
