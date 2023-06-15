<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function registerSeller(Request $request): JsonResponse
    {
        return $this->handleUserRegister($request, User::ROLES['SELLER']);
    }

    public function registerBuyer(Request $request): JsonResponse
    {
        return $this->handleUserRegister($request, User::ROLES['BUYER']);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credentials = request(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'password' => [
                        'Invalid credentials'
                    ],
                ]
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->first();
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return Response::json([
            'access_token' => $authToken,
        ]);
    }

    protected function handleUserRegister(Request $request, string $role): JsonResponse
    {
        $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $checkUser = User::where('email', $request->email)->first();

        if ($checkUser) {
            return response()->json([
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ],
                ]
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $role
        ]);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }
}
