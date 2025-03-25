<?php

namespace App\Http\Resources\Admin;

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
        return [
            'type' => 'user',
            'id' => $this->id,
            'attributes' => [
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'email' => $this->email,
                'username' => $this->username,
                'phone' => $this->phone,
                'age' => $this->age,
                'wallet' => $this->wallet,
                'credits' => $this->credits,
                'gender' => $this->gender,
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'dob' => $this->dob,
                'language' => $this->language,
                'avatar' => $this->avatar,
                'status' => $this->status,
                'active' => $this->active,
                'pushNotice' => $this->pushNotice,
                'is_admin' => $this->is_admin,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
//            'pagination' => [
//                'current_page' => $this->currentPage(),
//                'per_page' => $this->perPage(),
//                'total' => $this->total(),
//                'last_page' => $this->lastPage(),
//                'next_page_url' => $this->nextPageUrl(),
//                'prev_page_url' => $this->previousPageUrl(),
//            ],
        ];
    }
}
