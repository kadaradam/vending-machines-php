<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\RequestBody(
 *      request="LoginUserRequest",
 *      description="Login with user",
 *      required=true,
 *      @OA\JsonContent(
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
class LoginUserRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ];
    }
}
