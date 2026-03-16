<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\Auth\RegisterUserAction;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected RegisterUserAction $registerUser,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->registerUser->handle($request->validated());
        $token = Auth::guard('api')->login($user);

        return $this->tokenResponse($token, $user, 'User registered successfully', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = Auth::guard('api')->attempt($request->validated());

        if (! $token) {
            throw new ApiException('Invalid credentials', 401);
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();
        $user->load('subscription');

        return $this->tokenResponse($token, $user, 'User logged in successfully');
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }

    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();

        return response()->json([
            'user' => UserResource::make($user->load('subscription')),
        ]);
    }

    protected function tokenResponse(string $token, User $user, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => UserResource::make($user),
        ], $status);
    }
}
