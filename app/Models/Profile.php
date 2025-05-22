<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'gender',
        'age',
        'smoking_preference',
        'pet_preference',
        'cleanliness_level',
        'sleep_schedule',
        'hobbies',
        'academic_year',
        'major',
        'university_name',
        'looking_for_roommate',
        'profile_image',
    ];

    protected function casts(): array
    {
        return [
            'hobbies' => 'array',
            'looking_for_roommate' => 'boolean',
            'age' => 'integer',
            'cleanliness_level' => 'integer',
        ];
    }

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the profile image URL.
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        return $this->profile_image ? asset('storage/' . $this->profile_image) : null;
    }

    /**
     * Get formatted hobbies as a string.
     */
    public function getHobbiesStringAttribute(): string
    {
        return $this->hobbies ? implode(', ', $this->hobbies) : '';
    }

    /**
     * Calculate compatibility score with another profile.
     */
    public function calculateCompatibility(Profile $otherProfile): float
    {
        $score = 0;
        $maxScore = 0;

        // University compatibility (weight: 30)
        $maxScore += 30;
        if ($this->university_name === $otherProfile->university_name) {
            $score += 30;
        }

        // Academic year compatibility (weight: 10)
        $maxScore += 10;
        if ($this->academic_year === $otherProfile->academic_year) {
            $score += 10;
        }

        // Smoking preference compatibility (weight: 25)
        $maxScore += 25;
        if ($this->smoking_preference === 'flexible' || 
            $otherProfile->smoking_preference === 'flexible' ||
            $this->smoking_preference === $otherProfile->smoking_preference) {
            $score += 25;
        }

        // Pet preference compatibility (weight: 15)
        $maxScore += 15;
        if ($this->pet_preference === 'flexible' || 
            $otherProfile->pet_preference === 'flexible' ||
            $this->pet_preference === $otherProfile->pet_preference) {
            $score += 15;
        }

        // Cleanliness level compatibility (weight: 15)
        $maxScore += 15;
        if ($this->cleanliness_level && $otherProfile->cleanliness_level) {
            $difference = abs($this->cleanliness_level - $otherProfile->cleanliness_level);
            if ($difference <= 1) {
                $score += 15;
            } elseif ($difference <= 2) {
                $score += 10;
            }
        }

        // Sleep schedule compatibility (weight: 5)
        $maxScore += 5;
        if ($this->sleep_schedule === 'flexible' || 
            $otherProfile->sleep_schedule === 'flexible' ||
            $this->sleep_schedule === $otherProfile->sleep_schedule) {
            $score += 5;
        }

        return $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
    }

    /**
     * Scope to filter by gender.
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope to filter by university.
     */
    public function scopeByUniversity($query, $university)
    {
        return $query->where('university_name', $university);
    }

    /**
     * Scope to filter by age range.
     */
    public function scopeByAgeRange($query, $minAge, $maxAge)
    {
        return $query->whereBetween('age', [$minAge, $maxAge]);
    }

    /**
     * Scope to get profiles looking for roommates.
     */
    public function scopeLookingForRoommate($query)
    {
        return $query->where('looking_for_roommate', true);
    }
}