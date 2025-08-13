<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddFriendRequest;
use App\Services\FriendshipService;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function __construct(
        private FriendshipService $friendshipService
    ) {}

    public function addFriend(AddFriendRequest $request)
    {
        try {
            $result = $this->friendshipService->addFriend(
                auth()->user(),
                $request->validated('friend_id')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function friends(Request $request)
    {
        $friends = $this->friendshipService->getFriends(auth()->user());
        return response()->json(['friends' => $friends]);
    }
}