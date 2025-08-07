<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'subscription',
            'id' => $this->id,
            'attributes' => [
                'starts_at' => $this->starts_at?->toDateTimeString(),
                'ends_at' => $this->ends_at?->toDateTimeString(),
                'plan' => new PlanResource($this->plan),
                // 'user' => new UserResource($this->user),
            ],
        ];
    }
}
