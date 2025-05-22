<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicInfo extends Model
{
    use HasFactory;

    protected $table = 'academic_info';

    protected $fillable = [
        'university_name',
        'degree_name',
        'cut_off_mark',
        'year',
        'scholarship_name',
        'scholarship_description',
        'application_deadline',
        'link',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'cut_off_mark' => 'decimal:2',
            'year' => 'integer',
            'application_deadline' => 'date',
        ];
    }

    /**
     * Get formatted cut off mark.
     */
    public function getFormattedCutOffMarkAttribute(): string
    {
        return $this->cut_off_mark ? number_format($this->cut_off_mark, 2) : 'N/A';
    }

    /**
     * Check if application deadline is approaching (within 30 days).
     */
    public function getIsDeadlineApproachingAttribute(): bool
    {
        if (!$this->application_deadline) {
            return false;
        }

        return $this->application_deadline->diffInDays(now()) <= 30 && 
               $this->application_deadline->isFuture();
    }

    /**
     * Check if application deadline has passed.
     */
    public function getIsDeadlinePassedAttribute(): bool
    {
        if (!$this->application_deadline) {
            return false;
        }

        return $this->application_deadline->isPast();
    }

    /**
     * Get days until deadline.
     */
    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->application_deadline || $this->application_deadline->isPast()) {
            return null;
        }

        return $this->application_deadline->diffInDays(now());
    }

    /**
     * Scope to filter by university.
     */
    public function scopeByUniversity($query, $university)
    {
        return $query->where('university_name', 'like', '%' . $university . '%');
    }

    /**
     * Scope to filter by degree.
     */
    public function scopeByDegree($query, $degree)
    {
        return $query->where('degree_name', 'like', '%' . $degree . '%');
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to get upcoming deadlines.
     */
    public function scopeUpcomingDeadlines($query)
    {
        return $query->where('application_deadline', '>', now())
                    ->orderBy('application_deadline');
    }

    /**
     * Scope to get scholarships.
     */
    public function scopeScholarships($query)
    {
        return $query->where('type', 'beca')
                    ->whereNotNull('scholarship_name');
    }

    /**
     * Scope to get cut off marks.
     */
    public function scopeCutOffMarks($query)
    {
        return $query->where('type', 'notas-corte')
                    ->whereNotNull('cut_off_mark');
    }

    /**
     * Scope to search by multiple fields.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('university_name', 'like', '%' . $search . '%')
              ->orWhere('degree_name', 'like', '%' . $search . '%')
              ->orWhere('scholarship_name', 'like', '%' . $search . '%');
        });
    }
}