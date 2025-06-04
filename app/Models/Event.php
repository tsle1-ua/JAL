<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'end_datetime',
        'place_id',
        'user_id',
        'image_path',
        'category_id',
        'is_public',
        'price',
        'max_attendees',
        'current_attendees',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time' => 'datetime:H:i',
            'end_datetime' => 'datetime',
            'is_public' => 'boolean',
            'price' => 'decimal:2',
            'max_attendees' => 'integer',
            'current_attendees' => 'integer',
        ];
    }

    /**
     * Get the user that created the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the place where the event takes place.
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Get the category for the event.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Users attending this event.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_user')->withTimestamps();
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->price > 0 ? number_format($this->price, 2) . ' €' : 'Gratis';
    }

    /**
     * Get formatted date and time.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        $dateTime = $this->date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        
        if ($this->time) {
            $dateTime .= ' a las ' . $this->time->format('H:i');
        }

        return $dateTime;
    }

    /**
     * Get short formatted date.
     */
    public function getShortDateAttribute(): string
    {
        return $this->date->locale('es')->isoFormat('D MMM');
    }

    /**
     * Check if event is today.
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->date->isToday();
    }

    /**
     * Check if event is in the past.
     */
    public function getIsPastAttribute(): bool
    {
        if ($this->end_datetime) {
            return $this->end_datetime->isPast();
        }
        
        if ($this->time) {
            $eventDateTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                $this->date->format('Y-m-d') . ' ' . $this->time->format('H:i:s'));
            return $eventDateTime->isPast();
        }

        return $this->date->isPast();
    }

    /**
     * Check if event is upcoming (within next 7 days).
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->date->isFuture() && $this->date->diffInDays(now()) <= 7;
    }

    /**
     * Check if event has available spots.
     */
    public function getHasAvailableSpotsAttribute(): bool
    {
        if (!$this->max_attendees) {
            return true; // No limit
        }

        return $this->current_attendees < $this->max_attendees;
    }

    /**
     * Get available spots count.
     */
    public function getAvailableSpotsAttribute(): ?int
    {
        if (!$this->max_attendees) {
            return null; // No limit
        }

        return max(0, $this->max_attendees - $this->current_attendees);
    }

    /**
     * Get event location (place name or address).
     */
    public function getLocationAttribute(): string
    {
        if ($this->place) {
            return $this->place->name . ' - ' . $this->place->formatted_address;
        }

        return 'Ubicación por determinar';
    }

    /**
     * Get days until event.
     */
    public function getDaysUntilEventAttribute(): int
    {
        return max(0, $this->date->diffInDays(now()));
    }

    /**
     * Check if the authenticated user is attending.
     */
    public function getIsUserAttendingAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->attendees->contains(auth()->id());
    }

    /**
     * Scope to get public events.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
                    ->orderBy('date')
                    ->orderBy('time');
    }

    /**
     * Scope to get past events.
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString())
                    ->orderBy('date', 'desc');
    }

    /**
     * Scope to get events for a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope to get events in a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to get free events.
     */
    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    /**
     * Scope to get events with available spots.
     */
    public function scopeWithAvailableSpots($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_attendees')
              ->orWhereRaw('current_attendees < max_attendees');
        });
    }

    /**
     * Scope to search events.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    /**
     * Scope to get events in a specific city.
     */
    public function scopeInCity($query, $city)
    {
        return $query->whereHas('place', function ($q) use ($city) {
            $q->where('city', 'like', '%' . $city . '%');
        });
    }

    /**
     * Scope to get events within radius of coordinates.
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radius)
    {
        return $query->whereHas('place', function ($q) use ($latitude, $longitude, $radius) {
            $q->withinRadius($latitude, $longitude, $radius);
        });
    }
}