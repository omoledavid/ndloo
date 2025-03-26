<?php

namespace App\Support\Services\Auth;

use App\Contracts\Enums\UserStates;
use App\Http\Resources\UserResource;
use App\Models\AppToken;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\Auth\WelcomeNotice;
use App\Support\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmailVerificationService extends BaseService
{
    public function verifyCode(object $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::query()->create([
                ...Cache::get("REGISTRATION_DATA_{$request->email}"),
                'status' => UserStates::ACTIVE->value,
            ]);

            if ($request->query('appToken')) {
                AppToken::create([
                    'user_id' => $user->id,
                    'token' => $request->query('appToken'),
                ]);
            }

            //enroll user on free plan
            $user->subscriptions()->attach(SubscriptionPlan::first());

            DB::commit();

            $user->notify(new WelcomeNotice($user));

            return $this->successResponse(__('responses.emailVerified'), [
                'token' => $user->createToken('Auth token')->plainTextToken,
                'user' => new UserResource($user),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);

            return $this->errorResponse(__('responses.invalidCode'));
        }
    }
}
