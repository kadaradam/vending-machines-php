<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

/**
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 */
class UserController extends Controller
{
    /**
     * Display the actual logged in user.
     * 
     * @OA\Get(
     *     path="/user/me",
     *     tags={"User"},
     *     operationId="getUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *      response=200,
     *      ref="#/components/responses/UserResource"
     *     )
     * )
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Update your whole user profile in storage.
     * 
     * * @OA\Patch(
     *     path="/user/{userId}",
     *     tags={"User"},
     *     operationId="updateUserPatch",
     *     summary="Update your whole user profile in storage.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="The id of user to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Action is unauthorized"
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/UpdateUserRequestPatch")
     * )
     * 
     * Update your user profile property in storage.
     * 
     * @OA\Put(
     *     path="/user/{userId}",
     *     tags={"User"},
     *     operationId="updateUserPut",
     *     summary="Update your user profile property in storage.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="The id of user to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Action is unauthorized"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception"
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/UpdateUserRequestPut")
     * )
     */
    public function update(UpdateUserRequest $request)
    {
        $user = request()->user();

        User::where([
            ['id', "=", $user['id']],
        ])->update($request->validated());
    }

    /**
     * Remove your user from storage.
     * 
     * @OA\Delete(
     *     path="/user/{userId}",
     *     tags={"User"},
     *     operationId="deleteUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *          response=200,
     *          description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Action is unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     * )
     */
    public function destroy(DestroyUserRequest $request)
    {
        $user = $request->user();
        $user = User::where([
            ['id', "=", $user['id']],
        ])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Not Found!'
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        $user->delete();
    }
}
