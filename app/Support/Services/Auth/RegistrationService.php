<?php

namespace App\Support\Services\Auth;

use App\Contracts\DataObjects\User\CreateUserData;
use App\Contracts\Enums\OtpCodeTypes;
use App\Contracts\Enums\UserStates;
use App\Models\Country;
use App\Models\OtpCode;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\Auth\VerifyEmailNotice;
use App\Support\Helpers\SmsSender;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $user = User::query()->create(CreateUserData::fromRequest($request)->toArray());
            $user->update(['phone' => $phoneUtil->format($numberProto, PhoneNumberFormat::E164)]);
            $user->update(['password' => $request->password]);

            $token = rand(1000, 9999);

            OtpCode::create([
                'type' => OtpCodeTypes::VERIFICATION->value,
                'email' => $user->email,
                'token' => $token,
            ]);

            //enroll user in free plan
            $user->subscriptions()->attach(SubscriptionPlan::first());

            DB::commit();

            // $user->notify(new VerifyEmailNotice($user, $token));
            notify($user, 'EVER_CODE', [
                'code' => $token,
            ], ['email']);
            $smsSender->send($this->getMessage($token), $user->phone);

            return $this->successResponse(__('responses.userRegistered'), [
                'email' => $user->email,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

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
            $user = User::query()->create(CreateUserData::fromRequest($request)->toArray());
            $user->update(['phone' => $phoneUtil->format($numberProto, PhoneNumberFormat::E164)]);

            return $this->successResponse(data: [
                'email' => $user->email,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    public function passwordSignup(object $request, SmsSender $smsSender): JsonResponse
    {
        $user = User::query()->where('email', $request->email)->first();

        if ($user->status === UserStates::ACTIVE) {
            return $this->errorResponse(__('responses.invalidRequest'));
        }

        try {

            DB::beginTransaction();

            $token = rand(1000, 9999);
            $user->update(['password' => $request->password]);

            OtpCode::create([
                'type' => OtpCodeTypes::VERIFICATION->value,
                'email' => $user->email,
                'token' => $token,
            ]);

            //enroll user in free plan
            $user->subscriptions()->attach(SubscriptionPlan::first());

            DB::commit();

            // $user->notify(new VerifyEmailNotice($user, $token));
            notify($user, 'EVER_CODE', [
                'code' => $token,
            ], ['email']);
            $smsSender->send($this->getMessage($token), $user->phone);

            return $this->successResponse(__('responses.userRegistered'), [
                'email' => $user->email,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    public function getMessage(int $token): string
    {
        return "Use the code $token to verify your Ndloo account";
    }
}
