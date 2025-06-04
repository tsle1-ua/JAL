<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeisureZoneReview;
use Illuminate\Support\Facades\Storage;

class LeisureZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'city',
        'university',
        'latitude',
        'longitude',
        'user_id',
        'image_path',
        'is_promoted',
        'rating',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_promoted' => 'boolean',
            'rating' => 'decimal:2',
            'rating_count' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(LeisureZoneReview::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('s3')->url($this->image_path) : null;
    }
}
