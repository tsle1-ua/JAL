<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
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
            'address' => $this->address,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'price' => $this->price,
            'type' => $this->type,
            'square_meters' => $this->square_meters,
            'current_occupants' => $this->current_occupants,
            'max_occupants' => $this->max_occupants,
            'phone' => $this->phone,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'available_from' => $this->available_from,
            'is_available' => $this->is_available,
            'image_urls' => $this->image_urls,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
