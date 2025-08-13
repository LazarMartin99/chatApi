<?php

namespace App\Repositories;

use App\Models\Message;

class MessageRepository
{
    public function create(array $data): Message
    {
        $message = Message::create($data);
        return $message->load(['sender:id,name', 'receiver:id,name']);
    }

    public function getConversationBetweenUsers(int $userId1, int $userId2, int $perPage = 50)
    {
        return Message::where(function ($query) use ($userId1, $userId2) {
                        $query->where('sender_id', $userId1)
                              ->where('receiver_id', $userId2);
                    })
                    ->orWhere(function ($query) use ($userId1, $userId2) {
                        $query->where('sender_id', $userId2)
                              ->where('receiver_id', $userId1);
                    })
                    ->with(['sender:id,name', 'receiver:id,name'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }
}