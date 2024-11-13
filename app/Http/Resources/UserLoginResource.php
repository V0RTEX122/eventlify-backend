<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'profile_picture' => $this->user->profile_picture,
            'gender' => $this->user->gender,
            'birth_date' => $this->user->birth_date,
            'address' => $this->user->address,
            'created_at' => $this->user->created_at,
            'updated_at' => $this->user->updated_at,
            'deleted_at' => $this->user->deleted_at,
        ];
    }
}
