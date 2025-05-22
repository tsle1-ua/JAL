<?php

namespace App\Services;

use App\Models\User;
use App\Models\Match;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoomieMatchService
{
    public function getPotentialMatches(int $userId, int $limit = 10): Collection
    {
        $user = User::with('profile')->find($userId);
        
        if (!$user || !$user->profile || !$user->profile->looking_for_roommate) {
            return collect();
        }

        // Obtener usuarios que ya han sido evaluados (liked o disliked)
        $evaluatedUserIds = Match::where(function ($query) use ($userId) {
            $query->where('user_id_1', $userId)->whereIn('user_1_status', ['liked', 'disliked']);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_id_2', $userId)->whereIn('user_2_status', ['liked', 'disliked']);
        })->get()->map(function ($match) use ($userId) {
            return $match->user_id_1 == $userId ? $match->user_id_2 : $match->user_id_1;
        })->toArray();

        // Añadir el propio usuario a la lista de excluidos
        $evaluatedUserIds[] = $userId;

        // Buscar usuarios potenciales
        $potentialMatches = User::with('profile')
            ->whereHas('profile', function ($query) {
                $query->where('looking_for_roommate', true);
            })
            ->whereNotIn('id', $evaluatedUserIds)
            ->limit($limit * 3) // Obtenemos más para poder filtrar y ordenar
            ->get();

        // Calcular compatibilidad y ordenar
        $matchesWithCompatibility = $potentialMatches->map(function ($potentialMatch) use ($user) {
            $compatibility = $user->profile->calculateCompatibility($potentialMatch->profile);
            $potentialMatch->compatibility_score = $compatibility;
            return $potentialMatch;
        })->sortByDesc('compatibility_score')->take($limit);

        return $matchesWithCompatibility->values();
    }

    public function likeUser(int $fromUserId, int $toUserId): array
    {
        return DB::transaction(function () use ($fromUserId, $toUserId) {
            if ($fromUserId == $toUserId) {
                throw new \Exception('No puedes darte like a ti mismo.');
            }

            // Crear o actualizar el match
            $match = Match::createOrUpdateMatch($fromUserId, $toUserId, 'liked');

            $result = [
                'success' => true,
                'is_mutual_match' => $match->is_mutual_match,
                'match_id' => $match->id,
            ];

            if ($match->is_mutual_match) {
                $result['message'] = '¡Es un match! Ambos se han gustado mutuamente.';
                $result['other_user'] = $match->getOtherUser($fromUserId);
            } else {
                $result['message'] = 'Like enviado correctamente.';
            }

            return $result;
        });
    }

    public function dislikeUser(int $fromUserId, int $toUserId): bool
    {
        return DB::transaction(function () use ($fromUserId, $toUserId) {
            if ($fromUserId == $toUserId) {
                throw new \Exception('No puedes rechazarte a ti mismo.');
            }

            $match = Match::createOrUpdateMatch($fromUserId, $toUserId, 'disliked');
            return true;
        });
    }

    public function getMutualMatches(int $userId): Collection
    {
        $matches = Match::mutualMatches()
            ->forUser($userId)
            ->with(['user1.profile', 'user2.profile'])
            ->orderBy('matched_at', 'desc')
            ->get();

        return $matches->map(function ($match) use ($userId) {
            $otherUser = $match->getOtherUser($userId);
            $otherUser->matched_at = $match->matched_at;
            $otherUser->match_id = $match->id;
            return $otherUser;
        });
    }

    public function getPendingLikes(int $userId): Collection
    {
        // Usuarios que han dado like a este usuario pero aún no han recibido respuesta
        $pendingMatches = Match::where('user_id_2', $userId)
            ->where('user_2_status', 'pending')
            ->where('user_1_status', 'liked')
            ->with(['user1.profile'])
            ->get();

        return $pendingMatches->map(function ($match) {
            $user = $match->user1;
            $user->match_id = $match->id;
            return $user;
        });
    }

    public function getMatchHistory(int $userId): array
    {
        $allMatches = Match::forUser($userId)
            ->with(['user1.profile', 'user2.profile'])
            ->get();

        $history = [
            'total_likes_sent' => 0,
            'total_likes_received' => 0,
            'mutual_matches' => 0,
            'pending_responses' => 0,
        ];

        foreach ($allMatches as $match) {
            if ($match->user_id_1 == $userId) {
                if ($match->user_1_status == 'liked') {
                    $history['total_likes_sent']++;
                }
            } else {
                if ($match->user_2_status == 'liked') {
                    $history['total_likes_sent']++;
                }
            }

            if ($match->user_id_1 == $userId) {
                if ($match->user_2_status == 'liked') {
                    $history['total_likes_received']++;
                }
                if ($match->user_2_status == 'pending' && $match->user_1_status == 'liked') {
                    $history['pending_responses']++;
                }
            } else {
                if ($match->user_1_status == 'liked') {
                    $history['total_likes_received']++;
                }
                if ($match->user_1_status == 'pending' && $match->user_2_status == 'liked') {
                    $history['pending_responses']++;
                }
            }

            if ($match->is_mutual_match) {
                $history['mutual_matches']++;
            }
        }

        // Evitar contar matches mutuos dos veces
        $history['mutual_matches'] = $history['mutual_matches'] / 2;

        return $history;
    }

    public function removeMatch(int $userId, int $matchId): bool
    {
        return DB::transaction(function () use ($userId, $matchId) {
            $match = Match::find($matchId);
            
            if (!$match) {
                return false;
            }

            // Verificar que el usuario es parte del match
            if ($match->user_id_1 != $userId && $match->user_id_2 != $userId) {
                throw new \Exception('No tienes permisos para eliminar este match.');
            }

            return $match->delete();
        });
    }

    public function getCompatibilityFactors(int $userId, int $otherUserId): array
    {
        $user = User::with('profile')->find($userId);
        $otherUser = User::with('profile')->find($otherUserId);

        if (!$user || !$otherUser || !$user->profile || !$otherUser->profile) {
            return [];
        }

        $factors = [];

        // Universidad
        if ($user->profile->university_name && $otherUser->profile->university_name) {
            $factors['university'] = [
                'compatible' => $user->profile->university_name === $otherUser->profile->university_name,
                'user_value' => $user->profile->university_name,
                'other_value' => $otherUser->profile->university_name,
                'weight' => 30,
            ];
        }

        // Año académico
        if ($user->profile->academic_year && $otherUser->profile->academic_year) {
            $factors['academic_year'] = [
                'compatible' => $user->profile->academic_year === $otherUser->profile->academic_year,
                'user_value' => $user->profile->academic_year,
                'other_value' => $otherUser->profile->academic_year,
                'weight' => 10,
            ];
        }

        // Preferencia de fumar
        if ($user->profile->smoking_preference && $otherUser->profile->smoking_preference) {
            $compatible = $user->profile->smoking_preference === 'flexible' || 
                         $otherUser->profile->smoking_preference === 'flexible' ||
                         $user->profile->smoking_preference === $otherUser->profile->smoking_preference;
            
            $factors['smoking'] = [
                'compatible' => $compatible,
                'user_value' => $user->profile->smoking_preference,
                'other_value' => $otherUser->profile->smoking_preference,
                'weight' => 25,
            ];
        }

        // Preferencia de mascotas
        if ($user->profile->pet_preference && $otherUser->profile->pet_preference) {
            $compatible = $user->profile->pet_preference === 'flexible' || 
                         $otherUser->profile->pet_preference === 'flexible' ||
                         $user->profile->pet_preference === $otherUser->profile->pet_preference;
            
            $factors['pets'] = [
                'compatible' => $compatible,
                'user_value' => $user->profile->pet_preference,
                'other_value' => $otherUser->profile->pet_preference,
                'weight' => 15,
            ];
        }

        // Nivel de limpieza
        if ($user->profile->cleanliness_level && $otherUser->profile->cleanliness_level) {
            $difference = abs($user->profile->cleanliness_level - $otherUser->profile->cleanliness_level);
            $compatible = $difference <= 1;
            
            $factors['cleanliness'] = [
                'compatible' => $compatible,
                'user_value' => $user->profile->cleanliness_level,
                'other_value' => $otherUser->profile->cleanliness_level,
                'weight' => 15,
                'difference' => $difference,
            ];
        }

        // Horario de sueño
        if ($user->profile->sleep_schedule && $otherUser->profile->sleep_schedule) {
            $compatible = $user->profile->sleep_schedule === 'flexible' || 
                         $otherUser->profile->sleep_schedule === 'flexible' ||
                         $user->profile->sleep_schedule === $otherUser->profile->sleep_schedule;
            
            $factors['sleep_schedule'] = [
                'compatible' => $compatible,
                'user_value' => $user->profile->sleep_schedule,
                'other_value' => $otherUser->profile->sleep_schedule,
                'weight' => 5,
            ];
        }

        return $factors;
    }

    public function updateMatchPreferences(int $userId, array $preferences): bool
    {
        $user = User::with('profile')->find($userId);
        
        if (!$user || !$user->profile) {
            return false;
        }

        // Actualizar el estado de búsqueda de compañero
        if (isset($preferences['looking_for_roommate'])) {
            $user->profile->looking_for_roommate = $preferences['looking_for_roommate'];
            $user->profile->save();
        }

        return true;
    }

    public function getRecommendationScore(Profile $userProfile, Profile $candidateProfile): float
    {
        return $userProfile->calculateCompatibility($candidateProfile);
    }
}