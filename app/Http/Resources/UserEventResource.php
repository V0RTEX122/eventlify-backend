<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EventCreatorResource;
use App\Http\Resources\EventParticipantResource;

class UserEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
            'visibility' => $this->visibility,
            'created_by' => new EventCreatorResource($this->creator),
            'deleted_by' => $this->deleted_by,
            'participants' => EventParticipantResource::collection($this->participants),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
