<?php

namespace App\Contracts\DataObjects;

use Ramsey\Uuid\Uuid;

class TransactionData extends BaseData
{
    /**
     * Create a new TransactionData class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $reference,
        public readonly string $user_id,
        public readonly ?string $channel,
        public readonly string $icon,
        public readonly ?string $currency,
        public readonly string $amount,
        public readonly ?string $usdAmount
    ) {}

    public static function fromArray(array $data): self
    {
        return new static(
            name: $data['name'],
            reference: $data['reference'] ?? Uuid::uuid4(),
            user_id: $data['user_id'],
            channel: $data['channel'] ?? null,
            icon: $data['icon'],
            currency: $data['currency'] ?? 'USD',
            amount: $data['amount'],
            usdAmount: $data['usdAmount'] ?? $data['amount'],
        );
    }
}
