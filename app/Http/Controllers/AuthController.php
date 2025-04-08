<?php

namespace App\Http\Controllers;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (auth()->attempt($credentials)) {
            $user = Auth::user();
            /** @var User $user */
            $token = $user->createToken(env('APP_NAME'))->plainTextToken;
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::query()->create($validated);
        $token = $user->createToken(config('app.name'))->plainTextToken;
        return response()->json(['token' => $token], 201);
    }
}
