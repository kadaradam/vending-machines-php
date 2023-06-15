<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\RequestBody(
 *      request="UpdateUserRequestPatch",
 *      description="List of available properties to update",
 *      required=true,
 *      @OA\JsonContent(
 *          @OA\Property(
 *              property="username",
 *              type="string",
 *              description="The username of the user",
 *              example="JohnSnow"
 *          ),
 *          @OA\Property(
 *              property="email",
 *              type="string",
 *              description="The email of the user",
 *              example="john@example.com"
 *          )
 *      )
 * )
 * * @OA\RequestBody(
 *      request="UpdateUserRequestPut",
 *      description="List of available properties to update",
 *      required=true,
 *      @OA\JsonContent(
 *          required={"username", "email"},
 *          @OA\Property(
 *              property="username",
 *              type="string",
 *              description="The username of the user",
 *              example="JohnSnow"
 *          ),
 *          @OA\Property(
 *              property="email",
 *              type="string",
 *              description="The email of the user",
 *              example="john@example.com"
 *          )
 *      )
 * )
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $id = $this->route('user');
        $user = $this->user();
        return $id == $user['id'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $method = $this->method();

        if ($method == 'PUT') {
            return [
                'email' => ['required', 'email'],
                'username' => ['required', 'string'],
            ];
        } else {
            return [
                'email' => ['sometimes', 'required', 'email'],
                'username' => ['sometimes', 'required', 'string'],
            ];
        }
    }
}
