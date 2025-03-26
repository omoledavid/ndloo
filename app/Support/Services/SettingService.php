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
            // Check if the setting exists, then update it; otherwise, create it
            if ($this->settingRepository->exists($item)) {
                // Update existing setting
                $this->settingRepository->update($item, $value);
            } else {
                // Create new setting if it does not exist
                $this->settingRepository->create($item, $value);
            }
        }


        return $this->successResponse('Settings updated successfully', [
            'setting' => $this->settingRepository->all()
        ]);
    }
}
