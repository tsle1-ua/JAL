<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Subscription;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'role' => 'string',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user's academic preferences.
     */
    public function academicPreferences()
    {
        return $this->hasOne(AcademicPreference::class);
    }

    /**
     * Get the user's listings.
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Listings this user has marked as favorite.
     */
    public function favoriteListings()
    {
        return $this->belongsToMany(Listing::class, 'favorites')->withTimestamps();
    }

    /**
     * Get the user's events.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Events the user is attending.
     */
    public function attendingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')->withTimestamps();
    }

    /**
     * Get the user's places.
     */
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /**
     * Get the user's subscriptions (automatic rent payments).
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get matches where this user is user_1.
     */
    public function matchesAsUser1()
    {
        return $this->hasMany(Match::class, 'user_id_1');
    }

    /**
     * Get matches where this user is user_2.
     */
    public function matchesAsUser2()
    {
        return $this->hasMany(Match::class, 'user_id_2');
    }

    /**
     * Get all matches for this user.
     */
    public function allMatches()
    {
        return $this->matchesAsUser1->merge($this->matchesAsUser2);
    }

    /**
     * Get users that this user has liked.
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'matches', 'user_id_1', 'user_id_2')
                    ->wherePivot('user_1_status', 'liked');
    }

    /**
     * Get users that have liked this user.
     */
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'matches', 'user_id_2', 'user_id_1')
                    ->wherePivot('user_2_status', 'liked');
    }

    /**
     * Get mutual matches (both users liked each other).
     */
    public function mutualMatches()
    {
        return $this->belongsToMany(User::class, 'matches', 'user_id_1', 'user_id_2')
                    ->wherePivot('user_1_status', 'liked')
                    ->wherePivot('user_2_status', 'liked')
                    ->whereNotNull('matches.matched_at');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the user's full name with profile info.
     */
    public function getFullInfoAttribute(): string
    {
        $info = $this->name;
        if ($this->profile) {
            $info .= $this->profile->university_name ? ' - ' . $this->profile->university_name : '';
            $info .= $this->profile->major ? ' (' . $this->profile->major . ')' : '';
        }
        return $info;
    }

    /**
     * Scope to get users looking for roommates.
     */
    public function scopeLookingForRoommate($query)
    {
        return $query->whereHas('profile', function ($q) {
            $q->where('looking_for_roommate', true);
        });
    }

    /**
     * Scope to get users from a specific university.
     */
    public function scopeFromUniversity($query, $university)
    {
        return $query->whereHas('profile', function ($q) use ($university) {
            $q->where('university_name', $university);
        });
    }
}