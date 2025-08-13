<?php

namespace App\Repositories;

use App\Models\Friendship;

class FriendshipRepository
{
    public function create(int $userId, int $friendId): Friendship
    {
        return Friendship::create([
            'user_id' => $userId,
            'friend_id' => $friendId
        ]);
    }

    public function exists(int $userId, int $friendId): bool
    {
        return Friendship::where('user_id', $userId)
                        ->where('friend_id', $friendId)
                        ->exists();
    }
}