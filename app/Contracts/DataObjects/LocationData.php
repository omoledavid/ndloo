<?php

declare(strict_types=1);

namespace App\Contracts\DataObjects;

class LocationData extends BaseData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string|float $latitude,
        public readonly string|float $longitude,
    ) {}

    public static function fromRequest(object $request): self
    {
        return new static(
            $request->query('latitude'),
            $request->query('longitude')
        );
    }
}
