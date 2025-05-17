<?php

namespace App\Events;

use App\Models\Livestream;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LivestreamStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Livestream $livestream
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('livestream.'.$this->livestream->id),
            new Channel('livestreams'), // For global updates
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'status.changed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->livestream->id,
            'is_live' => $this->livestream->is_live,
            'started_at' => $this->livestream->started_at,
            'ended_at' => $this->livestream->ended_at,
            'user' => [
                'id' => $this->livestream->user->id,
                'username' => $this->livestream->user->username,
                'display_name' => $this->livestream->user->display_name,
            ],
        ];
    }
}