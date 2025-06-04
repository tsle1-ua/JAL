<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Subscription;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'city',
        'zip_code',
        'price',
        'type',
        'square_meters',
        'current_occupants',
        'max_occupants',
        'phone',
        'bedrooms',
        'bathrooms',
        'available_from',
        'is_available',
        'image_paths',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'bathrooms' => 'decimal:1',
            'available_from' => 'date',
            'is_available' => 'boolean',
            'image_paths' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'bedrooms' => 'integer',
            'square_meters' => 'integer',
            'current_occupants' => 'integer',
            'max_occupants' => 'integer',
        ];
    }

    /**
     * Get the user that owns the listing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Subscriptions for this listing.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Users who have favorited this listing.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * Get the first image URL.
     */
    public function getFirstImageUrlAttribute(): ?string
    {
        if ($this->image_paths && count($this->image_paths) > 0) {
            return asset('storage/' . $this->image_paths[0]);
        }
        return null;
    }

    /**
     * Get all image URLs.
     */
    public function getImageUrlsAttribute(): array
    {
        if (!$this->image_paths) {
            return [];
        }

        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $this->image_paths);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' €';
    }

    /**
     * Get formatted address.
     */
    public function getFormattedAddressAttribute(): string
    {
        return $this->address . ', ' . $this->city . ($this->zip_code ? ' (' . $this->zip_code . ')' : '');
    }

    /**
     * Get the display type in Spanish.
     */
    public function getDisplayTypeAttribute(): string
    {
        $types = [
            'apartamento' => 'Apartamento',
            'residencia' => 'Residencia',
            'habitacion' => 'Habitación',
            'casa' => 'Casa',
            'estudio' => 'Estudio',
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get the short description (first 150 characters).
     */
    public function getShortDescriptionAttribute(): string
    {
        return strlen($this->description) > 150 
            ? substr($this->description, 0, 150) . '...' 
            : $this->description;
    }

    /**
     * Get the availability status in Spanish.
     */
    public function getAvailabilityStatusAttribute(): string
    {
        return $this->is_available ? 'Disponible' : 'No disponible';
    }

    /**
     * Get the availability status with color class.
     */
    public function getAvailabilityBadgeAttribute(): string
    {
        return $this->is_available 
            ? '<span class="badge bg-success">Disponible</span>' 
            : '<span class="badge bg-danger">No disponible</span>';
    }

    /**
     * Get formatted bedrooms and bathrooms.
     */
    public function getRoomInfoAttribute(): string
    {
        $bedrooms = $this->bedrooms . ' ' . ($this->bedrooms == 1 ? 'habitación' : 'habitaciones');
        $bathrooms = $this->bathrooms . ' ' . ($this->bathrooms == 1 ? 'baño' : 'baños');

        return $bedrooms . ', ' . $bathrooms;
    }

    /**
     * Render occupant icons based on current occupants.
     */
    public function getOccupantIconsAttribute(): string
    {
        $icons = '';
        for ($i = 0; $i < $this->current_occupants; $i++) {
            $icons .= '<i class="bi bi-person-fill me-1"></i>';
        }
        return $icons ?: '<i class="bi bi-person"></i>';
    }

    /**
     * Get formatted available from date.
     */
    public function getFormattedAvailableFromAttribute(): string
    {
        return $this->available_from->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    }

    /**
     * Check if listing is available today.
     */
    public function getIsAvailableTodayAttribute(): bool
    {
        return $this->is_available && $this->available_from <= now()->toDateString();
    }

    /**
     * Get days until available.
     */
    public function getDaysUntilAvailableAttribute(): int
    {
        if ($this->available_from <= now()->toDateString()) {
            return 0;
        }
        
        return $this->available_from->diffInDays(now());
    }

    /**
     * Get price per bedroom.
     */
    public function getPricePerBedroomAttribute(): float
    {
        return $this->bedrooms > 0 ? round($this->price / $this->bedrooms, 2) : $this->price;
    }

    /**
     * Scope to get available listings.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to get unavailable listings.
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    /**
     * Scope to filter by city.
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', '%' . $city . '%');
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by price range.
     */
    public function scopePriceBetween($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to filter by minimum price.
     */
    public function scopeMinPrice($query, $minPrice)
    {
        return $query->where('price', '>=', $minPrice);
    }

    /**
     * Scope to filter by maximum price.
     */
    public function scopeMaxPrice($query, $maxPrice)
    {
        return $query->where('price', '<=', $maxPrice);
    }

    /**
     * Scope to filter by minimum bedrooms.
     */
    public function scopeMinBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', '>=', $bedrooms);
    }

    /**
     * Scope to filter by exact bedrooms.
     */
    public function scopeExactBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', $bedrooms);
    }

    /**
     * Scope to filter by minimum bathrooms.
     */
    public function scopeMinBathrooms($query, $bathrooms)
    {
        return $query->where('bathrooms', '>=', $bathrooms);
    }

    /**
     * Scope to get listings within a radius (in kilometers).
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
     * Scope to get listings available from a specific date.
     */
    public function scopeAvailableFrom($query, $date)
    {
        return $query->where('available_from', '<=', $date);
    }

    /**
     * Scope to get listings available after a specific date.
     */
    public function scopeAvailableAfter($query, $date)
    {
        return $query->where('available_from', '>', $date);
    }

    /**
     * Scope to search by title, description or address.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('address', 'like', '%' . $search . '%')
              ->orWhere('city', 'like', '%' . $search . '%');
        });
    }

    /**
     * Scope to order by price.
     */
    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    /**
     * Scope to order by creation date.
     */
    public function scopeOrderByNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order by available date.
     */
    public function scopeOrderByAvailableDate($query, $direction = 'asc')
    {
        return $query->orderBy('available_from', $direction);
    }

    /**
     * Scope to get listings by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get featured listings (could be based on premium status, etc.).
     */
    public function scopeFeatured($query)
    {
        // Por ahora, devolver listings más recientes como "destacados"
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get listings with images.
     */
    public function scopeWithImages($query)
    {
        return $query->whereNotNull('image_paths')
                    ->where('image_paths', '!=', '[]');
    }

    /**
     * Scope to get listings without images.
     */
    public function scopeWithoutImages($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('image_paths')
              ->orWhere('image_paths', '[]');
        });
    }

    /**
     * Calculate distance to given coordinates in kilometers.
     */
    public function calculateDistanceTo($latitude, $longitude): float
    {
        if (!$this->latitude || !$this->longitude) {
            return 0;
        }

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

        return round($earthRadius * $c, 2);
    }

    /**
     * Get a Google Maps URL for this listing.
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}";
        }
        
        $address = urlencode($this->formatted_address);
        return "https://www.google.com/maps/search/?api=1&query={$address}";
    }

    /**
     * Get Google Maps embed URL for this listing.
     */
    public function getGoogleMapsEmbedUrlAttribute(): string
    {
        $apiKey = config('services.google_maps.api_key');
        
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q={$this->latitude},{$this->longitude}";
        }
        
        $address = urlencode($this->formatted_address);
        return "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q={$address}";
    }

    /**
     * Check if the current user can edit this listing.
     */
    public function canBeEditedBy($user): bool
    {
        if (!$user) {
            return false;
        }
        
        return $this->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Check if the current user can delete this listing.
     */
    public function canBeDeletedBy($user): bool
    {
        if (!$user) {
            return false;
        }
        
        return $this->user_id === $user->id || $user->isAdmin();
    }
}