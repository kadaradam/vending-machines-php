<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * Display the actual logged in user.
     */
    public function me(Request $request)
    {
        return Response::json(new UserResource($request->user()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = request()->user();

        User::where([
            ['id', "=", $user['id']],
        ])->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
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
            ], 404);
        }

        $user->delete();
    }
}
