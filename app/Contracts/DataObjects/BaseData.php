<?php

declare(strict_types=1);

namespace App\Contracts\DataObjects;

use App\Contracts\Interfaces\DataInterface;

abstract class BaseData implements DataInterface
{
    //public abstract static function fromArray(array $data): self;
    //public abstract static function fromRequest(object $request): self;

    public function toArray(): array
    {
        return array_filter(get_object_vars($this));
    }
}
