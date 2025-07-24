<?php

namespace App\Services;

use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    use ApiResponseTrait;

    public function login(LoginRequest $request): JsonResponse
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->returnError("The provided credentials are incorrect.", Response::HTTP_BAD_REQUEST);
        }

        return $this->returnData(
            [
            'access_token' => $this->createUserToken($user),
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ],
        "User logged in successfully"
        );
    }

    private function createUserToken(User $user): string
    {
        return $user->createToken('SoftExpertTask')->plainTextToken;
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return $this->returnSuccessMessage('Logged out successfully.');
    }
}
