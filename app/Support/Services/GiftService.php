<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\DataObjects\NotificationData;
use App\Contracts\DataObjects\TransactionData;
use App\Contracts\Enums\GiftStatus;
use App\Contracts\Enums\TransactionIcons;
use App\Contracts\Enums\TransactionTypes;
use App\Http\Resources\GiftPlanResource;
use App\Jobs\PushNotificationJob;
use App\Models\GiftPlan;
use App\Models\Message;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserGift;
use App\Notifications\Gift\GiftReceivedNotice;
use App\Notifications\Gift\GiftSentNotice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftService extends BaseService
{
    public function plans(): JsonResponse
    {
        return $this->successResponse(data: [
            'plans' => GiftPlanResource::collection(GiftPlan::where('status', GiftStatus::ENABLE)->get()),
        ]);
    }

    public function myPlans(): JsonResponse
    {
        return $this->successResponse(data: [
            'plans' => UserGift::where('user_id', auth()->user()->id)->with('plan')->get(),
        ]);
    }

    public function purchase(GiftPlan $giftPlan, User $recipient): JsonResponse
    {
        try {
            Cache::lock(request()->user()->id, 10)->block(10, function () use ($giftPlan, $recipient) {
                $sender = User::where('id', request()->user()->id)->first();
                $senderBalanceAfter = floatval(bcsub(strval($sender->wallet), strval($giftPlan->amount), 2));
                $recipientBalanceAfter = floatval(bcadd(strval($recipient->wallet), strval($giftPlan->amount), 2));

                if ($senderBalanceAfter < 0) {
                    throw new \Exception(__('responses.insufficientFunds'));
                }

                DB::transaction(function () use ($giftPlan, $sender, $recipient, $senderBalanceAfter, $recipientBalanceAfter) {
                    $sender->update(['wallet' => $senderBalanceAfter]);
                    $recipient->update(['credits' => $recipientBalanceAfter]);

                    UserGift::create([
                        'user_id' => $recipient->id,
                        'sender_id' => $sender->id,
                        'gift_plan_id' => $giftPlan->id,
                    ]);

                    Transaction::create(TransactionData::fromArray([
                        'name' => TransactionTypes::GIFT_SENT->value,
                        'user_id' => $sender->id,
                        'amount' => $giftPlan->amount,
                        'icon' => TransactionIcons::GIFT->value,
                        'currency' => 'USD',
                        'usdAmount' => $giftPlan->amount,
                    ])->toArray());

                    Transaction::create(TransactionData::fromArray([
                        'name' => TransactionTypes::GIFT_RECEIVED->value,
                        'user_id' => $recipient->id,
                        'amount' => $giftPlan->amount,
                        'icon' => $giftPlan->icon,
                        'currency' => 'USD',
                        'usdAmount' => $giftPlan->amount,
                    ])->toArray());
                });
                notify($sender, 'GIFT_SENT', [
                    'amount' => $giftPlan->amount,
                    'recipient' => $recipient->firstname,
                ], ['email']);
                notify($recipient, 'GIFT_RECEIVED', [
                    'amount' => $giftPlan->amount,
                    'sender' => $sender->firstname,
                ], ['email']);
                $message = Message::create([
                    'sender' => $sender->id,
                    'recipient' => $recipient->id,
                    'type' => 'gift',
                    'content' => $giftPlan->name,
                    'gift_icon' => $giftPlan->icon,
                    //'media' => $media,
                ]);
                $message->update(['created_at' => date('Y-m-d H:i:s')]);
                $message->load('sender');

                PushNotificationJob::dispatchAfterResponse(
                    $recipient,
                    NotificationData::fromArray([
                        'title' => __('responses.newMessage'),
                        'body' => $sender->firstname . ': A gift has been sent',
                        'data' => []
                    ])
                );

                //                $sender->notify(new GiftSentNotice($recipient, $giftPlan->amount));
                //                $recipient->notify(new GiftReceivedNotice($sender, $giftPlan->amount));
            });

            return $this->successResponse(__('responses.giftSent'));
        } catch (\Throwable $th) {
            report($th);

            return $this->errorResponse($th->getMessage());
        }
    }

    public function redeem(UserGift $gift, Request $request): JsonResponse
    {
        $gift->load('plan');
        $giftIsMine = UserGift::where('user_id', $request->user()->id)->where('id', $gift->id)->where('status', '!=', 'redeemed')->first();
        if (!$giftIsMine) {
            return $this->errorResponse(__('responses.giftNotMine'));
        }

        try {

            DB::beginTransaction();
            $conversionCharge = ss('gift-conversion-charge');
            $giftAmount = $gift->plan->amount;

            // Calculate the charge amount (10%)
            $chargeAmount = ($giftAmount * $conversionCharge) / 100;

            // Subtract the charge from the gift amount
            $finalAmount = $giftAmount - $chargeAmount;

            // Update user's wallet
            $user = $request->user();
            $balAfter = $user->wallet + $finalAmount;
            $user->wallet = $balAfter;
            $user->save();



            //create transaction
            Transaction::create(TransactionData::fromArray([
                'name' => TransactionTypes::GIFT_SOLD->value,
                'user_id' => $user->id,
                'amount' => $gift->plan->amount / 2,
                'icon' => TransactionIcons::GIFT->value,
                'currency' => 'USD',
                'usdAmount' => $finalAmount,
            ])->toArray());

            $giftIsMine->status = 'redeemed';
            $giftIsMine->save();

            DB::commit();
            notify($user, 'GIFT_REDEEMED', [
                'gift-amount' => $giftAmount,
                'amount' => $finalAmount,
            ], ['email']);
            //            $request->user()->notify(new GiftRedeemedNotice($request->user(), $gift->plan->amount / 2));

            return $this->successResponse(__('responses.giftRedeemed'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorResponse(__('responses.unknownError'));
        }
    }
}
