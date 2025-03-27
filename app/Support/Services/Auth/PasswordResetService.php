<?php

declare(strict_types=1);

namespace App\Support\Services\Auth;

use App\Contracts\Enums\OtpCodeTypes;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\Auth\PasswordOtpNotice;
use App\Notifications\Auth\PasswordResetNotice;
use App\Support\Helpers\SmsSender;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;

class PasswordResetService extends BaseService
{
    public function sendCode(object $request, SmsSender $smsSender): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        $token = rand(1000, 9999);

        OtpCode::create([
            'type' => OtpCodeTypes::RESET->value,
            'email' => $request->email,
            'token' => $token,
        ]);

        //send otp via twilio sms service and email
        //$user->notify(new PasswordOtpNotice($user, $token));
        notify($user, 'PASSWORD_RESET', sendVia: ['email']);
        $smsSender->send($this->getMessage($token), $user->phone);

        return $this->successResponse(__('responses.otpSent'), [
            'email' => $request->email,
        ]);
    }

    public function verifyCode(object $request): JsonResponse
    {
        if (is_null($request->getData())) {
            return $this->errorResponse(__('responses.invalidCode'));
        }
        $request->getData()->delete();

        return $this->successResponse(data: [
            'email' => $request->email,
        ]);
    }

    public function changePassword(object $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if ($user->update(['password' => $request->password])) {
            //send password reset notice
            //$user->notify(new PasswordResetNotice($user));

            return $this->successResponse(__('responses.passwordChanged'));
        }

        return $this->errorResponse(__('responses.unknownError'));
    }

    public function getMessage(int $token): string
    {
        return "Use the code $token to reset your Ndloo account password";
    }
}
