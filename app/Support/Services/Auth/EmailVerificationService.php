<?php

namespace App\Support\Services\Auth;

use App\Contracts\Enums\UserStates;
use App\Http\Resources\UserResource;
use App\Models\AppToken;
use App\Models\User;
use App\Notifications\Auth\WelcomeNotice;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EmailVerificationService extends BaseService
{
    public function verifyCode(object $request): JsonResponse
    {
        try {
            $user = User::find($request->getAccount()?->user->id);
            $user->update(['status' => UserStates::ACTIVE->value]);
            $request->getAccount()->delete();

            if ($request->query('appToken')) {
                AppToken::create([
                    'user_id' => $user->id,
                    'token' => $request->query('appToken'),
                ]);
            }

            $user->notify(new WelcomeNotice($user));

            return $this->successResponse(__('responses.emailVerified'), [
                'token' => $user->createToken('Auth token')->plainTextToken,
                'user' => new UserResource($user),
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.invalidCode'));
        }
    }
}
