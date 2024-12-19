<?php

declare(strict_types=1);

namespace App\Support\Repositories;

use App\Contracts\Enums\SettingStates;
use App\Contracts\Interfaces\SettingInterface;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

class SettingRepository implements SettingInterface
{
    public function all(): ?Collection
    {
        return Setting::all();
    }

    public function tokenValue(): string|float
    {
        return Setting::query()
            ->where('item', SettingStates::BASE_EARNING->value)
            ->first()
            ->value;
    }

    public function update(string $item, string|float $value): ?bool
    {
        return Setting::query()
            ->where('item', $item)
            ->first()
            ->update(['value' => $value]);
    }
}
