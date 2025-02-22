<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentOption;
use App\Models\Setting;
use App\Models\SubscriptionCategory;
use App\Support\Services\BaseService;
use App\Support\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    public function gateWays(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|exists:payment_options,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slug' => 'nullable|unique:payment_options,slug',
            'status' => 'required|in:0,1',
        ]);

        $paymentOption = PaymentOption::where('name', $validatedData['name'])->first();

        if (!$paymentOption) {
            return $this->errorResponse('Payment gateway not found', 404);
        }

        // Handle logo upload if a new file is provided
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($paymentOption->logo) {
                Storage::disk('public')->delete($paymentOption->logo);
            }

            $logoPath = $request->file('logo')->store('logos', 'public'); // Store in `storage/app/public/logos`
            $validatedData['logo'] = $logoPath;
        } else {
            unset($validatedData['logo']); // Prevent overriding the existing logo with null
        }

        // Only update fields that are not null
        $filteredData = array_filter($validatedData, function ($value) {
            return !is_null($value);
        });

        $paymentOption->update($filteredData);

        return $this->successResponse('Payment gateway updated successfully');
    }
}
