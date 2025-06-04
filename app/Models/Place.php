<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'latitude',
        'longitude',
        'category',
        'user_id',
        'image_path',
        'is_verified',
        'rating',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_verified' => 'boolean',
            'rating' => 'decimal:2',
            'rating_count' => 'integer',
        ];
    }

    /**
     * Get the user that created the place.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the events at this place.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('s3')->url($this->image_path) : null;
    }

    /**
     * Get formatted address.
     */
    public function getFormattedAddressAttribute(): string
    {
        return $this->address . ', ' . $this->city;
    }

    /**
     * Get formatted rating.
     */
    public function getFormattedRatingAttribute(): string
    {
        return number_format($this->rating, 1) . '/5.0 (' . $this->rating_count . ' reseñas)';
    }

    /**
     * Get Google Maps embed URL.
     */
    public function getGoogleMapsEmbedUrlAttribute(): string
    {
        $apiKey = config('services.google_maps.api_key');
        return "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q={$this->latitude},{$this->longitude}";
    }

    /**
     * Get Google Maps link URL.
     */
    public function getGoogleMapsLinkAttribute(): string
    {
        return "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}";
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by city.
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', '%' . $city . '%');
    }

    /**
     * Scope to get verified places.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get places within a radius (in kilometers).
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radius)
    {
        return $query->selectRaw('*, 
            ( 6371 * acos( cos( radians(?) ) * 
            cos( radians( latitude ) ) * 
            cos( radians( longitude ) - radians(?) ) + 
            sin( radians(?) ) * 
            sin( radians( latitude ) ) ) ) AS distance', 
            [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance');
    }

    /**
     * Scope to order by rating.
     */
    public function scopeByRating($query, $order = 'desc')
    {
        return $query->orderBy('rating', $order);
    }

    /**
     * Scope to search by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('address', 'like', '%' . $search . '%');
        });
    }

    /**
     * Calculate distance to coordinates.
     */
    public function calculateDistanceTo($latitude, $longitude): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}