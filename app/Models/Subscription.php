<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'monthly_amount',
        'start_date',
        'next_payment_date',
        'payment_method',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
            'start_date' => 'date',
            'next_payment_date' => 'date',
            'active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
