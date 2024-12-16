<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return ['subscriptions' => $this->subscriptions,
            'profile' => $this->profile,
            'country' => $this->country,
            'images' => $this->images,
            'myLikes' => $this->myLikes()->where('actor', auth()->id())->pluck('recipient')->toArray(),
            'blockList' => $this->blockList->pluck('recipient')->toArray(),
            ...parent::toArray($request)];
    }
}
