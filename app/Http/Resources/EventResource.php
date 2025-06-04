<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'time' => $this->time?->format('H:i'),
            'end_datetime' => $this->end_datetime,
            'place_id' => $this->place_id,
            'category_id' => $this->category_id,
            'is_public' => $this->is_public,
            'price' => $this->price,
            'max_attendees' => $this->max_attendees,
            'current_attendees' => $this->current_attendees,
            'image_url' => $this->image_url,
            'location' => $this->location,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
