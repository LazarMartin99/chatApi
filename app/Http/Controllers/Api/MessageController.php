<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private MessageService $messageService
    ) {}

    public function send(SendMessageRequest $request)
    {
        try {
            $message = $this->messageService->sendMessage(
                auth()->user(),
                $request->validated('receiver_id'),
                $request->validated('content')
            );

            return response()->json([
                'message' => 'Üzenet elküldve',
                'data' => $message
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function conversation(Request $request, $userId)
    {
        try {
            $messages = $this->messageService->getConversation(
                auth()->user(),
                $userId
            );

            return response()->json([
                'messages' => $messages->items(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}