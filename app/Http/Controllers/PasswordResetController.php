<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\PasswordSignupRequest;
use App\Http\Requests\Auth\VerifyResetOtpRequest;
use App\Support\Helpers\SmsSender;
use App\Support\Services\Auth\PasswordResetService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function sendCode(EmailRequest $request, SmsSender $smsSender): JsonResponse
    {
        return $this->passwordResetService->sendCode($request, $smsSender);
    }

    public function verifyCode(VerifyResetOtpRequest $request): JsonResponse
    {
        return $this->passwordResetService->verifyCode($request);
    }

    public function changePassword(PasswordSignupRequest $request): JsonResponse
    {
        return $this->passwordResetService->changePassword($request);
    }
}
