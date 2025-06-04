<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\RoomMatch;

Broadcast::channel('match.{matchId}', function ($user, int $matchId) {
    return RoomMatch::where('id', $matchId)
        ->where(function ($q) use ($user) {
            $q->where('user_id_1', $user->id)
              ->orWhere('user_id_2', $user->id);
        })->exists();
});
