<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\FriendshipRepository;

class FriendshipService
{
    public function __construct(
        private UserRepository $userRepository,
        private FriendshipRepository $friendshipRepository
    ) {}

    public function addFriend(User $user, int $friendId): array
    {
        $friend = $this->userRepository->findActiveById($friendId);
        
        if (!$friend) {
            throw new \Exception('A felhasználó nem található vagy nem aktív');
        }

        if ($user->isFriendWith($friend)) {
            throw new \Exception('Már ismerősök');
        }

        // Kétirányú kapcsolat létrehozása
        $this->friendshipRepository->create($user->id, $friend->id);
        $this->friendshipRepository->create($friend->id, $user->id);

        return [
            'message' => 'Ismerős hozzáadva',
            'friend' => $friend->only(['id', 'name', 'email'])
        ];
    }

    public function getFriends(User $user)
    {
        return $user->friends()->get(['id', 'name', 'email', 'status']);
    }
}