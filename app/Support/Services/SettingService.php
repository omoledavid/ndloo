<?php

namespace App\Support\Services;

use App\Contracts\Interfaces\SettingInterface;
use Illuminate\Http\JsonResponse;

class SettingService extends BaseService
{
    public function __construct(private readonly SettingInterface $settingRepository)
    {
    }

    public function update(object $request): JsonResponse
    {
        foreach ($request->except('_token') as $item => $value) {
            $this->settingRepository->update($item, $value);
        }

        return $this->successResponse('Settings updated successfully', [
            'setting' => $this->settingRepository->all()
        ]);
    }
}
