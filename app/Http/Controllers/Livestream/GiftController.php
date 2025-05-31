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

        $livestream = Livestream::findOrFail($livestreamId);

        if (!$livestream->is_live) {
            return response()->json(['message' => 'Cannot send gifts to inactive livestreams'], 400);
        }

        $gift = GiftPlan::where('id',$request->gift_id)->first();
        if (!$gift) {
            return response()->json(['message' => 'Gift not found'], 404);
        }
        if ($gift->status != true) {
            return response()->json(['message' => 'Gift is not available'], 400);
        }
        $recipient = User::where('id',$livestream->user_id)->first();
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
