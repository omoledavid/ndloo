<?php

declare(strict_types=1);

namespace App\Contracts\DataObjects;

class NotificationData extends BaseData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly array $data,
    ) {}

    public static function fromArray(array $data): self
    {
        return new static(
            title: $data['title'],
            body: $data['body'],
            data: $data['data'] ?? []
        );
    }
}
