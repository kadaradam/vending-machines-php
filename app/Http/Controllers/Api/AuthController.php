<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    /**
     * Registers a seller user.
     * 
     * @OA\Post(
     *     path="/register/seller",
     *     tags={"Auth"},
     *     operationId="registerSellerUser",
     *     @OA\RequestBody(ref="#/components/requestBodies/RegisterUserRequest"),
     *     @OA\Response(
     *          response=200,
     *          ref="#/components/responses/UserResource"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function registerSeller(RegisterUserRequest $request): JsonResponse
    {
        return $this->handleUserRegister($request, User::ROLES['SELLER']);
    }

    /**
     * Registers a buyer user.
     * 
     * @OA\Post(
     *     path="/register/buyer",
     *     tags={"Auth"},
     *     operationId="registerBuyerUser",
     *     @OA\RequestBody(ref="#/components/requestBodies/RegisterUserRequest"),
     *     @OA\Response(
     *          response=200,
     *          ref="#/components/responses/UserResource"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function registerBuyer(RegisterUserRequest $request): JsonResponse
    {
        return $this->handleUserRegister($request, User::ROLES['BUYER']);
    }

    /**
     * Authenticates a user.
     * 
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     operationId="loginUser",
     *     @OA\RequestBody(ref="#/components/requestBodies/LoginUserRequest"),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="accessToken",
     *                     type="string"
     *                 ),
     *                 example={"accessToken": "2|DDUPRhVGMIUtXFIs1S0axZqeMXcGSpOPn1lJ4NS5"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
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
            'accessToken' => $authToken,
        ]);
    }

    protected function handleUserRegister(Request $request, string $role): JsonResponse
    {
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
