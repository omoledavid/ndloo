<?php

namespace App\Http\Resources\livestream;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestreamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'livestreams',
            'id' => (string) $this->id,
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'thumbnail' => $this->thumbnail,
                'stream_key' => $this->stream_key,
                'stream_url' => $this->stream_url,
                'is_live' => $this->is_live,
                'started_at' => $this->started_at ? $this->started_at->toIso8601String() : null,
                'ended_at' => $this->ended_at ? $this->ended_at->toIso8601String() : null,
                'viewer_count' => $this->viewer_count,
                'ticket_amount' => $this->ticket_amount,
                'goal_title' => $this->goal_title,
                'goal_amount' => $this->goal_amount,
                'key_words' => $this->key_words,
            ],
            'relationships' => [
                'user' =>  $this->whenLoaded('user'),
                'categories' => [
                    'data' => $this->whenLoaded('categories', function () {
                        return $this->categories->map(function ($category) {
                            return [
                                'type' => 'categories',
                                'id' => (string) $category->id,
                                'attributes' => [
                                    'name' => $category->name,
                                    'description' => $category->description,
                                ],
                            ];
                        });
                    }),
                ],
            ],
            'links' => [
                'self' => route('livestreams.show', ['id' => $this->id]),
            ],
            'meta' => [
                'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
                'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            ],
        ];
    }
}
