<?php

namespace App\Events;

use App\Models\GiftTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewGiftTransaction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public GiftTransaction $transaction
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('livestream.'.$this->transaction->livestream_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new.gift';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->transaction->id,
            'quantity' => $this->transaction->quantity,
            'created_at' => $this->transaction->created_at,
            'gift' => [
                'id' => $this->transaction->gift->id,
                'name' => $this->transaction->gift->name,
                'icon' => $this->transaction->gift->icon,
            ],
            'sender' => [
                'id' => $this->transaction->sender->id,
                'username' => $this->transaction->sender->username,
                'display_name' => $this->transaction->sender->display_name,
            ],
            'receiver' => [
                'id' => $this->transaction->receiver->id,
                'username' => $this->transaction->receiver->username,
                'display_name' => $this->transaction->receiver->display_name,
            ],
        ];
    }
}