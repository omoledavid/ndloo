<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
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
            'tranzak-app-id' => 'nullable',
            'tranzak-api-key' => 'nullable',
            'tranzak-api-url' => 'nullable',
            'tranzak-return-url' => 'nullable',
            'tranzak-webhook-auth-key' => 'nullable',
            'agora-app-id' => 'nullable',
            'agora-app-certificate' => 'nullable',
            'twilio-sid' => 'nullable',
            'twilio-auth-token' => 'nullable',
            'twilio-number' => 'nullable',
            'gift-conversion-charge' => 'nullable|integer|min:1|max:100',
            'withdrawal_charge' => 'nullable|integer|min:1|max:100',



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
    public function allEmailTemplate()
    {
        $emailTemplates = NotificationTemplate::query()->get();

        return $this->successResponse('success',data:[
            'emailTemplates' => $emailTemplates,
        ]);
    }
    public function viewTemplate($id)
    {
        $template = NotificationTemplate::query()->find($id);
        return $this->successResponse('success',data:[
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'codes' => json_decode($template->shortcodes, true),
                'body' => $template->email_body,
            ],
        ]);
    }
    public function editTemplate(Request $request, $id)
    {
        $template = NotificationTemplate::query()->find($id);
        $template->update([
            'email_body' => $request->input('body'),
        ]);
        return $this->successResponse('Template updated successfully');
    }
}
