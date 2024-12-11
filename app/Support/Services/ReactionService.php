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
        if (! $this->validateAction($request->query('action'))) {
            return $this->errorResponse(__('responses.invalidAction'));
        }

        $reactions = Reaction::query()
            ->where([
                ['type', $request->query('action')],
                ['recipient', $request->user()->id],
            ])
            ->with(['actor', 'actor.country', 'actor.profile', 'actor.images'])
            ->get();

        return $this->successResponse(data: [
            'reactions' => ReactionResource::collection($reactions),
        ]);
    }

    public function toggleReaction(object $request, User $recipient): JsonResponse
    {
        if (! $this->validateAction($request->query('action'))) {
            return $this->errorResponse(__('responses.invalidAction'));
        }

        if ($request->query('action') === 'dislike') {
            Reaction::query()->where([
                ['type', 'like'],
                ['actor', $request->user()->id],
                ['recipient', $recipient->id],
            ])->delete();
        }

        if ($request->query('action') === 'like') {
            Reaction::query()->where([
                ['type', 'dislike'],
                ['actor', $request->user()->id],
                ['recipient', $recipient->id],
            ])->delete();
        }

        $record = Reaction::query()->where([
            ['type', $request->query('action')],
            ['actor', $request->user()->id],
            ['recipient', $recipient->id],
        ])->first();

        if (is_null($record)) {
            Reaction::create([
                'type' => $request->query('action'),
                'actor' => $request->user()->id,
                'recipient' => $recipient->id,
            ]);
            
            if ($request->query('action') === 'like') {
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

            $record->delete();
        }

        return $this->successResponse(__('responses.reactionToggled'));
    }

    private function validateAction(string $action): bool
    {
        return in_array($action, ReactionTypes::values(), true);
    }
}
