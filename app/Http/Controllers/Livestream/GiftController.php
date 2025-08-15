<?php

namespace App\Http\Controllers\Livestream;

use App\Events\NewGiftTransaction;
use App\Http\Controllers\Controller;
use App\Models\GiftPlan;
use App\Models\GiftTransaction;
use App\Models\Livestream;
use App\Models\User;
use App\Support\Services\GiftService;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function __construct(private readonly GiftService $giftService) {}
    public function index()
    {
        $gifts = GiftPlan::where('status', true)->get();

        return response()->json($gifts);
    }

    public function sendGift(Request $request, $livestreamId)
    {
        $request->validate([
            'gift_id' => 'required|exists:gift_plans,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);
        $sender = auth()->user();
        if (!$sender) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $giftPlan = GiftPlan::findOrFail($request->gift_id);
        if ($sender->wallet < $giftPlan->amount * $request->quantity) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }
        if (!$sender->active) {
            return response()->json(['message' => 'Your account is inactive'], 403);
        }
        if (!$sender->status) {
            return response()->json(['message' => 'Your account is not verified'], 403);
        }

        $livestream = Livestream::query()->where('id', $livestreamId)->first();
        if (!$livestream) {
            return response()->json(['message' => 'Livestream not found'], 404);
        }
        if ($livestream->user_id == $sender->id) {
            return response()->json(['message' => 'You cannot send gifts to your own livestream'], 400);
        }
        $livestream->update(['goal_progress' => $livestream->goal_progress + ($giftPlan->amount * $request->quantity)]);

        if (!$livestream->is_live) {
            return response()->json(['message' => 'Cannot send gifts to inactive livestreams'], 400);
        }

        $gift = GiftPlan::where('id', $request->gift_id)->first();
        if (!$gift) {
            return response()->json(['message' => 'Gift not found'], 404);
        }
        if ($gift->status != true) {
            return response()->json(['message' => 'Gift is not available'], 400);
        }
        $recipient = User::where('id', $livestream->user_id)->first();
        if (!$recipient) {
            return response()->json(['message' => 'Recipient not found'], 404);
        }
        if ($request->quantity < 1 || $request->quantity > 100) {
            return response()->json(['message' => 'Quantity must be between 1 and 100'], 400);
        }


        $this->giftService->purchase($gift, $recipient);

        $transaction = GiftTransaction::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $livestream->user_id,
            'livestream_id' => $livestream->id,
            'gift_id' => $gift->id,
            'quantity' => $request->quantity,
        ]);

        // Load relationships for broadcasting
        $transaction->load(['gift', 'sender', 'receiver']);

        // Broadcast the new gift transaction event
        broadcast(new NewGiftTransaction($transaction));

        return response()->json([
            'message' => 'Gift sent successfully',
            'transaction' => $transaction->load(['gift', 'sender', 'receiver']),
            'goal_progress' => $livestream->goal_progress
        ], 201);
    }

    public function getGiftTransactions($livestreamId)
    {
        $livestream = Livestream::findOrFail($livestreamId);

        $transactions = $livestream->giftTransactions()
            ->with(['gift', 'sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }
}
