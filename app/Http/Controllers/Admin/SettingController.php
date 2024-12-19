<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SubscriptionCategory;
use App\Support\Services\BaseService;
use App\Support\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends BaseService
{
    public function __construct(private readonly SettingService $settingService)
    {

    }
    public function getSettings()
    {
        return $this->successResponse(data:[
            'settings' => Setting::all(),
        ]);
    }
    public function updateSettings(Request $request)
    {
        $request->validate([
            'tranzak-app-id' => 'required',
            'tranzak-api-key' => 'required',
            'tranzak-api-url' => 'required',
            'tranzak-return-url' => 'required',
            'tranzak-webhook-auth-key' => 'required',
            'agora-app-id' => 'required',
            'agora-app-certificate' => 'required',
            'twilio-sid' => 'required',
            'twilio-auth-token' => 'required',
            'twilio-number' => 'required',

        ]);
        return $this->settingService->update($request);
    }
    public function getCategories()
    {
        return $this->successResponse(data:[
            'categories' => SubscriptionCategory::all(),
        ]);
    }
}
