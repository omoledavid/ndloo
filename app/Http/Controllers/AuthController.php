<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\DetailSignupRequest;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\LoginOtpRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordSignupRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Support\Helpers\SmsSender;
use App\Support\Services\Auth\EmailVerificationService;
use App\Support\Services\Auth\LoginService;
use App\Support\Services\Auth\RegistrationService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function getCountries(RegistrationService $registrationService): JsonResponse
    {
        return $registrationService->getCountries();
    }

    public function allSignup(
        SignupRequest $request,
        RegistrationService $registrationService,
        SmsSender $smsSender
    ): JsonResponse {
        return $registrationService->signup($request, $smsSender);
    }

    public function detailSignup(
        DetailSignupRequest $request,
        RegistrationService $registrationService
    ): JsonResponse {
        return $registrationService->detailSignup($request);
    }

    public function passwordSignup(
        PasswordSignupRequest $request,
        RegistrationService $registrationService,
        SmsSender $smsSender
    ): JsonResponse {
        return $registrationService->passwordSignup($request, $smsSender);
    }

    public function verifyCode(
        VerifyEmailRequest $request,
        EmailVerificationService $emailVerificationService
    ): JsonResponse {
        return $emailVerificationService->verifyCode($request);
    }

    public function login(LoginRequest $request, LoginService $loginService): JsonResponse
    {
        return $loginService->login($request);
    }

    public function otpLogin(
        EmailRequest $request,
        LoginService $loginService,
        SmsSender $smsSender
    ): JsonResponse {
        return $loginService->otpLogin($request, $smsSender);
    }

    public function verifyOtp(LoginOtpRequest $request, LoginService $loginService): JsonResponse
    {
        return $loginService->verifyOtp($request);
    }
}
