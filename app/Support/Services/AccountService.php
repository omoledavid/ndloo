<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AccountService extends BaseService
{
    public function changeLanguage(object $request): JsonResponse
    {
        $request->user()->update(['language' => $request->language]);

        return $this->successResponse(__('responses.languageChanged'));
    }

    public function toggleNotifications(object $request): JsonResponse
    {
        $request->user()->update(['pushNotice' => ! $request->user()->pushNotice]);

        return $this->successResponse();
    }

    public function changePassword(object $request): JsonResponse
    {
        if (Hash::check($request->oldPassword, $request->user()->password)) {
            $request->user()->update(['password' => $request->password]);

            return $this->successResponse(__('responses.passwordChanged'));
        }

        return $this->errorResponse(__('responses.wrongPassword'));
    }

    public function changeEmail(object $request): JsonResponse
    {
        if (Hash::check($request->password, $request->user()->password)) {
            $request->user()->update(['email' => $request->email]);

            return $this->successResponse(__('responses.emailChanged'));
        }

        return $this->errorResponse(__('responses.wrongPassword'));
    }

    public function getTransactions(): JsonResponse
    {
        return $this->successResponse(data: [
            'transactions' => Transaction::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('created_at'),
        ]);
    }
    
    public function deleteAccount(object $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        $request->user()->forceDelete();

        return $this->successResponse('Account deleted');
    }
}
