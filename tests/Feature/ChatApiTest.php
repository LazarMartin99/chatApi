<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Friendship;
use App\Models\Message;
use App\Enums\UserStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        Event::fake();

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => ['id', 'name', 'email']
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'status' => UserStatus::INACTIVE->value
        ]);

        Event::assertDispatched(Registered::class);
    }

    public function test_user_can_add_friend()
    {
        $user1 = User::factory()->active()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->active()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user1, 'sanctum')
                        ->postJson('/api/friends/add', [
                            'friend_id' => $user2->id
                        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Ismerős hozzáadva']);

        $this->assertDatabaseHas('friendships', [
            'user_id' => $user1->id,
            'friend_id' => $user2->id
        ]);

        $this->assertDatabaseHas('friendships', [
            'user_id' => $user2->id,
            'friend_id' => $user1->id
        ]);
    }

    public function test_friends_can_send_messages()
    {
        $user1 = User::factory()->active()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->active()->create(['email_verified_at' => now()]);

        // Ismerős kapcsolat létrehozása
        Friendship::create(['user_id' => $user1->id, 'friend_id' => $user2->id]);
        Friendship::create(['user_id' => $user2->id, 'friend_id' => $user1->id]);

        $response = $this->actingAs($user1, 'sanctum')
                        ->postJson('/api/messages/send', [
                            'receiver_id' => $user2->id,
                            'content' => 'Hello friend!'
                        ]);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Üzenet elküldve']);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'content' => 'Hello friend!'
        ]);
    }

    public function test_non_friends_cannot_send_messages()
    {
        $user1 = User::factory()->active()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->active()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user1, 'sanctum')
                        ->postJson('/api/messages/send', [
                            'receiver_id' => $user2->id,
                            'content' => 'Hello stranger!'
                        ]);

        $response->assertStatus(403)
                ->assertJson(['message' => 'Csak ismerősöknek küldhet üzenetet']);
    }

    public function test_user_can_list_active_users()
    {
        $activeUser = User::factory()->active()->create(['email_verified_at' => now()]);
        $inactiveUser = User::factory()->create(['email_verified_at' => now()]); // inactive by default
        $currentUser = User::factory()->active()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($currentUser, 'sanctum')
                        ->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'users',
                    'pagination'
                ]);

        // Csak az aktív felhasználó szerepeljen a listában (saját maga nem)
        $users = $response->json('users');
        $this->assertCount(1, $users);
        $this->assertEquals($activeUser->id, $users[0]['id']);
    }
}