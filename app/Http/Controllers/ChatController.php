<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\User;
use App\Support\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private readonly ChatService $chatService) {}

    public function recentMessages(Request $request): JsonResponse
    {
        return $this->chatService->recentMessages($request);
    }

    public function sendMessage(MessageRequest $request, User $recipient): JsonResponse
    {
        return $this->chatService->sendMessage($request, $recipient);
    }

    public function chatMessages(Request $request, User $user): JsonResponse
    {
        return $this->chatService->chatMessages($request, $user);
    }

    public function markRead(Request $request, User $user): JsonResponse
    {
        return $this->chatService->markRead($request, $user);
    }
}
