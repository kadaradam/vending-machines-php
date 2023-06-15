<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Response(
 *      response="UserResource",
 *      description="All information about the user",
 *      @OA\JsonContent(
 *          @OA\Property(
 *              property="data",
 *              type="object",
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  description="The id of the user",
 *                  example="1"
 *              ),
 *              @OA\Property(
 *                  property="username",
 *                  type="string",
 *                  description="The username of the user",
 *                  example="JohnSnow"
 *              ),
 *              @OA\Property(
 *                  property="email",
 *                  type="string",
 *                  description="The email of the user",
 *                  example="john@example.com"
 *              ),
 *              @OA\Property(
 *                  property="role",
 *                  type="string",
 *                  description="The role of the user",
 *                  example="seller",
 *                  enum={"seller", "buyer"}
 *              ),
 *              @OA\Property(
 *                  property="createdAt",
 *                  type="timestamp",
 *                  description="User created timestamp",
 *                  example="2014-01-01T23:28:56.782Z"
 *              ),
 *              @OA\Property(
 *                  property="updatedAt",
 *                  type="timestamp",
 *                  description="User updated timestamp",
 *                  example="2014-01-01T23:28:56.782Z"
 *              )
 *         )
 *      )
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
