<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'feature',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'label' => $this->label,
                'value' => $this->pivot?->limit ?? 0,
            ],
        ];
    }
}
