<?php

declare(strict_types=1);

namespace App\Support\Services\Auth;

use App\Contracts\Enums\OtpCodeTypes;
use App\Contracts\Enums\UserStates;
use App\Http\Resources\UserResource;
use App\Models\AppToken;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\Auth\LoginOtpNotice;
use App\Support\Helpers\SmsSender;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginService extends BaseService
{
    public function otpLogin(object $request, SmsSender $smsSender): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        $token = rand(1000, 9999);
        if ($user) {
            notify($user, 'EVER_CODE', [
                'code' => $token,
            ], ['email']);
        }

        OtpCode::create([
            'type' => OtpCodeTypes::LOGIN->value,
            'email' => $user->email,
            'token' => $token,
        ]);

        //send otp via twilio sms service and email
        $user->notify(new LoginOtpNotice($user, $token));
        $smsSender->send($this->getMessage($token), $user->phone);

        return $this->successResponse(__('responses.otpSent'), [
            'email' => $request->email,
        ]);
    }

    public function getMessage(int $token): string
    {
        return "Use the code $token to sign in to your Ndloo account";
    }

    public function verifyOtp(object $request): JsonResponse
    {
        if (is_null($request->getAccount())) {
            return $this->errorResponse(__('responses.invalidCode'));
        }

        $user = User::find($request->getAccount()->user->id);
        $request->getAccount()->delete();

        if ($user->status !== UserStates::ACTIVE->value) {
            return $this->successResponse(data: [
                'verified' => false,
                'user' => new UserResource($user),
            ]);
        }

        Auth::login($user);
        return $this->loginResponse($request);
    }

    public function login(object $request): JsonResponse
    {
        if (Auth::attempt($request->authData())) {
            if (Auth::user()->status !== UserStates::ACTIVE->value) {
                return $this->successResponse(data: [
                    'verified' => false,
                    'user' => new UserResource(Auth::user()),
                ]);
            }

            return $this->loginResponse($request);
        }

        return $this->errorResponse(__('responses.invalidCredentials'));
    }

    public function loginResponse(Request $request): JsonResponse
    {
        if ($request->query('appToken')) {
            AppToken::create([
                'user_id' => Auth::user()->id,
                'token' => $request->query('appToken'),
            ]);
        }

        $token = Auth::user()->createToken('Auth token')->plainTextToken;

        return $this->successResponse(data: [
            'verified' => true,
            'token' => $token,
            'user' => new UserResource(Auth::user()),
        ]);
    }
}
