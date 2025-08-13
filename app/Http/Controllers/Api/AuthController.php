<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Regisztráció sikeres! Kérjük, erősítse meg email címét.',
            'user' => $user->only(['id', 'name', 'email'])
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return response()->json([
                'message' => 'Hibás bejelentkezési adatok vagy nem megerősített email cím'
            ], 401);
        }

        return response()->json([
            'message' => 'Sikeres bejelentkezés',
            'token' => $result['token'],
            'user' => $result['user']->only(['id', 'name', 'email', 'status'])
        ]);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        return $this->authService->verifyEmail($id, $hash);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Kijelentkezés sikeres']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email', 'status'])
        ]);
    }
}