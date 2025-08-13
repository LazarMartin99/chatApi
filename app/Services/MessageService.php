<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use App\Repositories\MessageRepository;

class MessageService
{
    public function __construct(
        private MessageRepository $messageRepository
    ) {}

    public function sendMessage(User $sender, int $receiverId, string $content): Message
    {
        $receiver = User::findOrFail($receiverId);

        if (!$sender->isFriendWith($receiver)) {
            throw new \Exception('Csak ismerősöknek küldhet üzenetet');
        }

        return $this->messageRepository->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'content' => $content
        ]);
    }

    public function getConversation(User $user, int $otherUserId, int $perPage = 50)
    {
        $otherUser = User::findOrFail($otherUserId);

        if (!$user->isFriendWith($otherUser)) {
            throw new \Exception('Csak ismerősökkel folytathat beszélgetést');
        }

        return $this->messageRepository->getConversationBetweenUsers($user->id, $otherUserId, $perPage);
    }
}