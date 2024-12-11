<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Services\ReactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function __construct(private readonly ReactionService $reactionService) {}

    public function reactions(Request $request): JsonResponse
    {
        return $this->reactionService->reactions($request);
    }

    public function reactionsToMe(Request $request): JsonResponse
    {
        return $this->reactionService->reactionsToMe($request);
    }

    public function toggleReaction(Request $request, User $recipient): JsonResponse
    {
        return $this->reactionService->toggleReaction($request, $recipient);
    }
}
