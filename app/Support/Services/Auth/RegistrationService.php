<?php

namespace App\Support\Services\Auth;

use App\Contracts\DataObjects\User\CreateUserData;
use App\Models\Country;
use App\Notifications\Auth\VerifyEmailNotice;
use App\Support\Helpers\SmsSender;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class RegistrationService extends BaseService
{
    public function getCountries(): JsonResponse
    {
        return $this->successResponse(data: [
            'countries' => Country::all(),
        ]);
    }

    public function signup(object $request, SmsSender $smsSender): JsonResponse
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $numberProto = $phoneUtil->parse($request->phone, Country::find($request->country)?->iso);

        if (! $phoneUtil->isValidNumber($numberProto)) {
            return $this->errorResponse(__('responses.invalidPhoneNo'));
        }

        try {
            $signupData = [
                ...CreateUserData::fromRequest($request)->toArray(),
                'phone'    => $phoneUtil->format($numberProto, PhoneNumberFormat::E164),
                'password' => $request->password
            ];

            $token = rand(1000, 9999);

            Cache::put("REGISTRATION_DATA_{$request->email}", $signupData, now()->addMinutes(15));
            Cache::put("EMAIL_VERIFICATION_{$request->email}", $token, now()->addMinutes(15));

            Notification::route('mail', $request->email)->notify(
                new VerifyEmailNotice($request->email, $request->firstname, $token)
            );
            $smsSender->send($this->getMessage($token), $request->phone);

            return $this->successResponse(__('responses.userRegistered'), [
                'email' => $request->email,
            ]);
        } catch (\Throwable $th) {
           report($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    public function detailSignup(object $request): JsonResponse
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $numberProto = $phoneUtil->parse($request->phone, Country::find($request->country)?->iso);

        if (! $phoneUtil->isValidNumber($numberProto)) {
            return $this->errorResponse(__('responses.invalidPhoneNo'));
        }

        try {
            $signupData = [
                ...CreateUserData::fromRequest($request)->toArray(),
                'phone'    => $phoneUtil->format($numberProto, PhoneNumberFormat::E164),
            ];

            Cache::put("REGISTRATION_DATA_{$request->email}", $signupData, now()->addHour());

            return $this->successResponse(data: [
                'email' => $request->email,
            ]);
        } catch (\Throwable $th) {
            report($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    public function passwordSignup(object $request, SmsSender $smsSender): JsonResponse
    {
        try {
            $regData = Cache::get("REGISTRATION_DATA_{$request->email}");

            $signupData =  [
                ...$regData,
                'password' => $request->password,
            ];
            
            $token = rand(1000, 9999);

            Cache::put("REGISTRATION_DATA_{$request->email}", $signupData, now()->addMinutes(15));
            Cache::put("EMAIL_VERIFICATION_{$request->email}", $token, now()->addMinutes(15));

            Notification::route('mail', $request->email)->notify(
                new VerifyEmailNotice($request->email, $regData->firstname, $token)
            );
            $smsSender->send($this->getMessage($token), $request->phone);

            return $this->successResponse(__('responses.userRegistered'), [
                'email' => $request->email,
            ]);
        } catch (\Throwable $th) {
            report($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    public function getMessage(int $token): string
    {
        return "Use the code $token to verify your Ndloo account";
    }
}
