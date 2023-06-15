<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\RequestBody(
 *      request="RegisterUserRequest",
 *      description="Register a new user",
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
 *          ),
 *          @OA\Property(
 *              property="password",
 *              type="string",
 *              description="The password of the user",
 *              example="Example123"
 *          )
 *      )
 * )
 */
class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ];
    }
}
