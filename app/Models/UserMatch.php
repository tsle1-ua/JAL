<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id_1',
        'user_id_2',
        'user_1_status',
        'user_2_status',
        'matched_at',
    ];

    protected function casts(): array
    {
        return [
            'matched_at' => 'datetime',
        ];
    }

    /**
     * Get the first user.
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    /**
     * Get the second user.
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    /**
     * Messages exchanged within this match.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Check if this is a mutual match.
     */
    public function getIsMutualMatchAttribute(): bool
    {
        return $this->user_1_status === 'liked' && 
               $this->user_2_status === 'liked' && 
               $this->matched_at !== null;
    }

    /**
     * Get the other user in the match for a given user ID.
     */
    public function getOtherUser($userId)
    {
        if ($this->user_id_1 == $userId) {
            return $this->user2;
        } elseif ($this->user_id_2 == $userId) {
            return $this->user1;
        }
        return null;
    }

    /**
     * Get the status for a specific user.
     */
    public function getStatusForUser($userId): string
    {
        if ($this->user_id_1 == $userId) {
            return $this->user_1_status;
        } elseif ($this->user_id_2 == $userId) {
            return $this->user_2_status;
        }
        return 'pending';
    }

    /**
     * Update status for a specific user.
     */
    public function updateStatusForUser($userId, $status)
    {
        if ($this->user_id_1 == $userId) {
            $this->user_1_status = $status;
        } elseif ($this->user_id_2 == $userId) {
            $this->user_2_status = $status;
        }

        // Check if it's a mutual match
        if ($this->user_1_status === 'liked' && $this->user_2_status === 'liked' && !$this->matched_at) {
            $this->matched_at = now();
        }

        $this->save();
    }

    /**
     * Create or update a match between two users.
     */
    public static function createOrUpdateMatch($user1Id, $user2Id, $status)
    {
        // Ensure consistent ordering (smaller ID first)
        if ($user1Id > $user2Id) {
            [$user1Id, $user2Id] = [$user2Id, $user1Id];
        }

        $match = self::firstOrCreate([
            'user_id_1' => $user1Id,
            'user_id_2' => $user2Id,
        ]);

        $match->updateStatusForUser($user1Id, $status);

        return $match;
    }

    /**
     * Scope to get mutual matches.
     */
    public function scopeMutualMatches($query)
    {
        return $query->where('user_1_status', 'liked')
                    ->where('user_2_status', 'liked')
                    ->whereNotNull('matched_at');
    }

    /**
     * Scope to get matches for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id_1', $userId)
                    ->orWhere('user_id_2', $userId);
    }

    /**
     * Scope to get pending matches for a user.
     */
    public function scopePendingForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id_1', $userId)->where('user_1_status', 'pending');
        })->orWhere(function ($q) use ($userId) {
            $q->where('user_id_2', $userId)->where('user_2_status', 'pending');
        });
    }

    /**
     * Scope to get liked matches for a user.
     */
    public function scopeLikedByUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id_1', $userId)->where('user_1_status', 'liked');
        })->orWhere(function ($q) use ($userId) {
            $q->where('user_id_2', $userId)->where('user_2_status', 'liked');
        });
    }
}