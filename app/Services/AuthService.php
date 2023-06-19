<?php

namespace App\Services;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthService
{
  /**
   * Register a user.
   *
   * @param  RegisterUserRequest  $request
   * @param  string  $role
   * @return User
   * 
   * @throws HttpException
   * @throws UnprocessableEntityHttpException
   */
  public function register(RegisterUserRequest $request, string $role): User
  {
    $checkUser = User::where('email', $request->email)->first();

    if ($checkUser) {
      throw abort(
        HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
        'The email has already been taken.'
      );
    }

    $user = User::create([
      'username' => $request->username,
      'email' => $request->email,
      'password' => bcrypt($request->password),
      'role' => $role
    ]);

    return $user;
  }

  /**
   * Login a user.
   *
   * @param  LoginUserRequest  $request
   * @return string
   *
   * @throws HttpException
   * @throws UnauthorizedHttpException
   */
  public function login(LoginUserRequest $request): string
  {
    $credentials = request(['email', 'password']);

    if (!auth()->attempt($credentials)) {
      throw abort(HttpResponse::HTTP_UNAUTHORIZED, 'Invalid credentials');
    }

    $user = User::where('email', $request->email)->first();
    $authToken = $user->createToken('auth-token')->plainTextToken;

    return $authToken;
  }
}
