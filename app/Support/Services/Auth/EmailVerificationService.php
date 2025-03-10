<?php

namespace App\Support\Services\Auth;

use App\Contracts\Enums\UserStates;
use App\Http\Resources\UserResource;
use App\Models\AppToken;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\Auth\WelcomeNotice;
use App\Support\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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

            //$user->notify(new WelcomeNotice($user));
            $this->autoSub($user);

            return $this->successResponse(__('responses.emailVerified'), [
                'token' => $user->createToken('Auth token')->plainTextToken,
                'user' => new UserResource($user),
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.invalidCode'));
        }
    }
    private function autoSub($user)
    {
        try {
            DB::beginTransaction();
            $plan = SubscriptionPlan::query()->where('name', '6 months')->first();

            $expiryDate = Carbon::now()->addMonth(6)->toDateTimeString();
            $user->update(['wallet' => $user->wallet - 0]);
            $user->subscriptions()->detach();
            $plan->user()->attach($user, ['expires_on' => $expiryDate]);

            DB::commit();

            //$request->user()->notify(new SubscriptionNotice($request->user(), $plan, $expiryDate));

            return $this->successResponse(__('responses.planSubscribed', ['name' => $plan->name.' '.$plan->category?->name]));
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }
}
