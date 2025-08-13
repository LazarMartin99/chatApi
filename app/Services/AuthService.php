<?php

namespace App\Services;

use App\Models\User;
use App\Enums\UserStatus;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function register(array $data): User
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => UserStatus::INACTIVE,
        ]);

        event(new Registered($user));

        return $user;
    }

    public function login(array $credentials): ?array
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return null;
        }

        // Aktív státuszra állítás
        $this->userRepository->update($user->id, ['status' => UserStatus::ACTIVE]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user->fresh()
        ];
    }

    public function verifyEmail(int $id, string $hash): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Felhasználó nem található.'
            ], 404);
        }

        // Hash ellenőrzése
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json([
                'message' => 'Érvénytelen verifikációs link.'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email cím már meg van erősítve.'
            ], 400);
        }

        // Email verifikálása és aktív státusz beállítása
        $user->markEmailAsVerified();
        $this->userRepository->update($user->id, ['status' => UserStatus::ACTIVE]);

        return response()->json([
            'message' => 'Email sikeresen megerősítve! Most már bejelentkezhetsz.',
            'user' => $user->only(['id', 'name', 'email', 'status'])
        ]);
    }

}