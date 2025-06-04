<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeisureZoneReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'leisure_zone_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function zone()
    {
        return $this->belongsTo(LeisureZone::class, 'leisure_zone_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
