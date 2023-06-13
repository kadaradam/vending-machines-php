<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
