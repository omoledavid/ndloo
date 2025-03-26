<?php

declare(strict_types=1);

namespace App\Contracts\DataObjects\User;

use App\Contracts\DataObjects\BaseData;
use App\Contracts\Enums\UserType;

class CreateUserData extends BaseData
{
    public function __construct(
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $email,
        public readonly string $username,
        public readonly string|int $age,
        public readonly string $phone,
        public readonly string|int $type,
        public readonly string|int $country_id,
        public readonly string $gender,
        public readonly string|float|null $latitude = null,
        public readonly string|float|null $longitude = null
    ) {}

    public static function fromRequest(object $request): self
    {
        return new static(
            $request->firstname,
            $request->lastname,
            $request->email,
            explode('@', $request->email)[0],
            $request->age,
            $request->phone,
            $request->type,
            $request->country,
            $request->gender,
            $request->latitude,
            $request->longitude
        );
    }
}
