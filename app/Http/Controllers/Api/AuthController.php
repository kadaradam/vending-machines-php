<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  AuthService  $authService
     * @return void
     */
    public function __construct(private AuthService $authService)
    {
        //
    }

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
        $user = $this->authService->register($request, User::ROLES['SELLER']);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
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
        $user = $this->authService->register($request, User::ROLES['BUYER']);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
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
        $authToken = $this->authService->login($request);

        return Response::json([
            'accessToken' => $authToken,
        ]);
    }
}
