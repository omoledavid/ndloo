<?php


namespace App\Contracts\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface SettingInterface
{
    public function all(): ?Collection;

    public function tokenValue(): string|float;

    public function update(string $item, string|float $value): ?bool;

    public function exists(int|string $item);

    public function create(int|string $item, mixed $value);
}
