<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\DataObjects\NotificationData;
use App\Contracts\Enums\MessageStates;
use App\Http\Resources\MessageResource;
use App\Jobs\PushNotificationJob;
use App\Models\Message;
use App\Models\User;
use App\Support\Helpers\FileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ChatService extends BaseService
{
    private const LIMIT = 10000;

    public function recentMessages(object $request): JsonResponse
    {
        $results = collect();

        $baseQuery = Message::query()
            ->where('sender', auth()->user()->id)
            ->orWhere('recipient', auth()->user()->id)
            ->with(['sender', 'recipient'])
            ->latest()
            ->limit(self::LIMIT);

        $messages = $request->query('offset')
            ? $baseQuery->offset($request->query('offset'))->get()//->reverse()
            : $baseQuery->get();

        $users = array_unique(array_merge($messages->groupBy('sender')->keys()->toArray(), $messages->groupBy('recipient')->keys()->toArray()));

        foreach ($users as $user_id) {
            if ($user_id !== auth()->user()->id) {
                $results->push([
                    'unreadMessages' => Message::where([
                        ['sender', $user_id],
                        ['recipient', auth()->user()->id],
                        ['read', MessageStates::UNREAD],
                    ])->count(),
                    'user' => User::with(['images', 'profile'])->find($user_id),
                    'messages' => Message::where([
                        ['sender', $user_id],
                        ['recipient', auth()->user()->id],
                    ])
                        ->orWhere([
                            ['sender', auth()->user()->id],
                            ['recipient', $user_id],
                        ])->with(['sender', 'recipient'])->orderBy('created_at', 'desc')->limit(100)->get(),
                ]);
            }
        }
        /*
        $filteredMessages = $messages->groupBy(['sender', 'recipient'])->get(auth()->user()->id) ?? [];

        foreach ($filteredMessages as $user_id => $messages) {
            $results->push([
                'unreadMessages' => Message::Where([
                    ['sender', $user_id],
                    ['recipient', auth()->user()->id],
                    ['read', MessageStates::UNREAD],
                ])->count(),
                'user' => User::with(['images', 'profile'])->find($user_id),
                'messages' => $messages,
            ]);
        }*/

        return $this->successResponse(data: [
            'messages' => $results,
        ]);
    }

    public function sendMessage(object $request, User $recipient): JsonResponse
    {
        try {
            /* $media = [];

             //upload any chat media
             if ($request->file('media')) {
                 foreach ($request->file('media') as $medium) {
                     $media[] = FileUpload::uploadFile($medium, folder: 'chat');
                 }
             }*/

            //check message subscription and count
            $currentSubscription = $request->user()->subscriptions[0];

            if ($currentSubscription->pivot->message_count >= $currentSubscription->message_count) {
                return $this->errorResponse(__('responses.messageCountReached'));
            }

            //check for censored messages
            $censoredWords = ['number', 'phone', 'tel', 'digits', 'telephone'];
            $censoredResults = array_intersect($censoredWords, explode(' ', $request->message));

            if (count($censoredResults) > 0) {
                return $this->errorResponse(__('responses.invalidContent', [
                    'contents' => implode(',', $censoredResults),
                ]));
            }

            $message = Message::create([
                'sender' => $request->user()->id,
                'recipient' => $recipient->id,
                'content' => $request->message,
                //'media' => $media,
            ]);
            $message->update(['created_at' => date('Y-m-d H:i:s')]);
            $request->user()->subscriptions()->syncWithoutDetaching($currentSubscription, [
                'message_count' => $currentSubscription->pivot->message_count + 1,
            ]);
            $message->load('sender');
            
            PushNotificationJob::dispatchAfterResponse(
                $recipient,
                NotificationData::fromArray([
                    'title' => __('responses.newMessage'),
                    'body' => $request->user()->firstname.': '.$request->message,
                    'data' => []
                ])
            );

            return $this->successResponse(data: [
                'message' => $message,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    public function chatMessages(object $request, User $user): JsonResponse
    {
        $messages = Message::query()
            ->where([
                ['sender', auth()->user()->id],
                ['recipient', $user->id],
            ])
            ->orWhere([
                ['sender', $user->id],
                ['recipient', auth()->user()->id],
            ])
            ->with(['sender', 'recipient', 'sender.images', 'recipient.images', 'sender.profile', 'recipient.profile'])
            ->orderBy('created_at', 'asc')
            ->limit(self::LIMIT);

        return $this->successResponse(data: [
            'messages' => MessageResource::collection(
                $request->query('offset')
                    ? $messages->offset($request->query('offset'))->get()
                    : $messages->get()
            ),
        ]);
    }

    public function markRead(object $request, User $user): JsonResponse
    {
        Message::where([
            ['sender', $user->id],
            ['recipient', $request->user()->id],
            ['read', MessageStates::UNREAD],
        ])->update(['read' => MessageStates::READ]);

        return $this->successResponse();
    }
}
