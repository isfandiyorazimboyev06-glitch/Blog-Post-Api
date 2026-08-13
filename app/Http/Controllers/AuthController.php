<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

use App\Http\Resources\UserResource;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    // Inject the AuthService through the constructor
    public function __construct(
        protected AuthService $authService
    ){}

    // Register User
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ],201);
    }

    // Login User
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'user'=> new UserResource($result['user']),
            'token'=> $result['token']
        ]);

    }

    // Logout User
    public function logout(Request $request):JsonResponse
    {
        // Delete the token that was used to authorize the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ],200);
    }
}
