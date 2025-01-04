<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\DataObjects\NotificationData;
use App\Contracts\Enums\ReactionTypes;
use App\Http\Resources\ReactionResource;
use App\Jobs\PushNotificationJob;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ReactionService extends BaseService
{
    public function reactions(object $request): JsonResponse
    {
        if (! $this->validateAction($request->query('action'))) {
            return $this->errorResponse(__('responses.invalidAction'));
        }

        $reactions = Reaction::query()
            ->where([
                ['type', $request->query('action')],
                ['actor', $request->user()->id],
            ])
            ->orderBy('id', 'desc')
            ->with(['recipient', 'recipient.country', 'recipient.profile', 'recipient.images'])
            ->get();

        return $this->successResponse(data: [
            'reactions' => ReactionResource::collection($reactions),
        ]);
    }

    public function reactionsToMe(object $request): JsonResponse
    {
        // Validate the action to ensure it's either 'like' or 'dislike'
        $action = $request->query('action');
        if (! in_array($action, ['like', 'dislike'])) {
            return $this->errorResponse(__('responses.invalidAction'));
        }

        // Ensure that the user is valid (e.g., logged-in user exists)
        $userId = $request->user()->id;
        if (empty($userId)) {
            return $this->errorResponse(__('responses.invalidUser'));
        }

        // Perform the query to fetch reactions
        $reactions = Reaction::query()
            ->where('type', $action)  // No need for array, use a simple 'where' method
            ->where('recipient', $userId)  // No need for array, use a simple 'where' method
            ->with(['actor', 'actor.country', 'actor.profile', 'actor.images'])
            ->get();

        // Return success response with reactions data
        return $this->successResponse(data: [
            'reactions' => ReactionResource::collection($reactions),
        ]);
    }



    public function toggleReaction(object $request, User $recipient): JsonResponse
    {
        $action = $request->query('action');

        // Validate action value
        if (! in_array($action, ['like', 'dislike'])) {
            return $this->errorResponse(__('responses.invalidAction'));
        }


        // Remove opposite reaction if exists
        $oppositeAction = ($action === 'like') ? 'dislike' : 'like';

        // Check if opposite reaction exists and delete it
        $deletedCount = Reaction::query()
            ->where([
                ['type', '=', $oppositeAction],
                ['actor', '=', $request->user()->id],
                ['recipient', '=', $recipient->id],
            ])
            ->delete();

        // Log the number of deleted reactions
        \Log::info("Deleted $deletedCount reactions of type: $oppositeAction");

        // Check if the reaction already exists for this action
        $record = Reaction::query()->where([
            ['type', '=', $action],
            ['actor', '=', $request->user()->id],
            ['recipient', '=', $recipient->id],
        ])->first();

        if (is_null($record)) {
            // Log creation action
            \Log::info("Creating new reaction of type: $action");

            Reaction::create([
                'type' => $action,
                'actor' => $request->user()->id,
                'recipient' => $recipient->id,
            ]);

            if ($action === 'like') {
                PushNotificationJob::dispatchAfterResponse(
                    $recipient,
                    NotificationData::fromArray([
                        'title' => __('responses.newLike'),
                        'body' => __('responses.likeMessage', ['name' => $request->user()->firstname]),
                        'data' => []
                    ])
                );
            }
        } else {
            // Log deletion action
            \Log::info("Deleting existing reaction of type: $action");

            $record->delete();
        }

        return $this->successResponse(__('responses.reactionToggled'));
    }



    private function validateAction(string $action): bool
    {
        return in_array($action, ReactionTypes::values(), true);
    }
}
