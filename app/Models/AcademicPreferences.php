<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'desired_university',
        'desired_degree_type',
        'current_academic_year',
        'interests',
        'scholarship_notifications',
        'cut_off_notifications',
    ];

    protected function casts(): array
    {
        return [
            'interests' => 'array',
            'scholarship_notifications' => 'boolean',
            'cut_off_notifications' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the academic preferences.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted interests as a string.
     */
    public function getInterestsStringAttribute(): string
    {
        return $this->interests ? implode(', ', $this->interests) : '';
    }

    /**
     * Get recommended academic info based on preferences.
     */
    public function getRecommendedAcademicInfo()
    {
        $query = AcademicInfo::query();

        if ($this->desired_university) {
            $query->where('university_name', 'like', '%' . $this->desired_university . '%');
        }

        if ($this->desired_degree_type) {
            $query->where('degree_name', 'like', '%' . $this->desired_degree_type . '%');
        }

        return $query->orderBy('created_at', 'desc')->limit(10)->get();
    }

    /**
     * Get upcoming scholarship deadlines based on preferences.
     */
    public function getUpcomingScholarships()
    {
        if (!$this->scholarship_notifications) {
            return collect();
        }

        $query = AcademicInfo::scholarships()->upcomingDeadlines();

        if ($this->desired_university) {
            $query->where('university_name', 'like', '%' . $this->desired_university . '%');
        }

        return $query->limit(5)->get();
    }

    /**
     * Get relevant cut off marks based on preferences.
     */
    public function getRelevantCutOffMarks()
    {
        if (!$this->cut_off_notifications) {
            return collect();
        }

        $query = AcademicInfo::cutOffMarks();

        if ($this->desired_university) {
            $query->where('university_name', 'like', '%' . $this->desired_university . '%');
        }

        if ($this->desired_degree_type) {
            $query->where('degree_name', 'like', '%' . $this->desired_degree_type . '%');
        }

        return $query->orderBy('year', 'desc')->limit(5)->get();
    }
}