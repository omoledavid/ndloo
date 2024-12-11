<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LanguageRequest;
use App\Support\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    public function changeLanguage(LanguageRequest $request): JsonResponse
    {
        return $this->accountService->changeLanguage($request);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        return $this->accountService->changePassword($request);
    }

    public function changeEmail(ChangeEmailRequest $request): JsonResponse
    {
        return $this->accountService->changeEmail($request);
    }

    public function toggleNotifications(Request $request): JsonResponse
    {
        return $this->accountService->toggleNotifications($request);
    }

    public function getTransactions(): JsonResponse
    {
        return $this->accountService->getTransactions();
    }
    
    public function deleteAccount(Request $request): JsonResponse
    {
        return $this->accountService->deleteAccount();
    }
}
