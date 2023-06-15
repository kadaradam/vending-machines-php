<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
  use RefreshDatabase;
  use WithFaker;

  /**
   * @var array
   */
  private $routes;

  /**
   * Setup the test.
   *
   * @return void
   */
  public function setUp(): void
  {
    parent::setUp();

    $this->routes = [
      'register/seller' => '/api/register/seller',
      'register/buyer' => '/api/register/buyer',
      'login' => '/api/login',
    ];
  }

  /**
   * A seller user can signup successfully.
   *
   * @return void
   */
  public function testASellerUserCanSignupSuccessfully()
  {
    Event::fake();

    $request = [
      'email' => $this->faker->safeEmail(),
      'username' => $this->faker->userName(),
      'password' => '123456',
    ];

    $this
      ->json('POST', $this->routes['register/seller'], $request)
      ->assertStatus(Response::HTTP_CREATED)
      ->assertJsonStructure([
        'data' => [
          'id',
          'username',
          'email',
          'updatedAt',
          'createdAt',
          'role'
        ]
      ])
      ->assertJson([
        'data' => [
          'email' => $request['email'],
          'username' => $request['username'],
          'role' => User::ROLES['SELLER']
        ]
      ]);
  }

  /**
   * A buyer user can signup successfully.
   *
   * @return void
   */
  public function testABuyerUserCanSignupSuccessfully()
  {
    Event::fake();

    $request = [
      'email' => $this->faker->safeEmail(),
      'username' => $this->faker->userName(),
      'password' => '123456',
    ];

    $this
      ->json('POST', $this->routes['register/buyer'], $request)
      ->assertStatus(Response::HTTP_CREATED)
      ->assertJsonStructure([
        'data' => [
          'id',
          'username',
          'email',
          'updatedAt',
          'createdAt',
          'role'
        ]
      ])
      ->assertJson([
        'data' => [
          'email' => $request['email'],
          'username' => $request['username'],
          'role' => User::ROLES['BUYER']
        ]
      ]);
  }

  /**
   * A user can not signup without required input.
   *
   * @return void
   */
  public function testAUserCanNotSignupWithoutRequiredInput()
  {
    $this
      ->json('POST', $this->routes['register/seller'], [])
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
      ->assertJsonValidationErrorFor('username')
      ->assertJsonValidationErrorFor('email')
      ->assertJsonValidationErrorFor('password');
  }

  /**
   * A user can not signup with conflicting email.
   *
   * @return void
   */
  public function testAUserCanNotSignupWithConflictingEmail()
  {
    $user = User::factory()->create();

    $request = [
      'email' => $user->email,
      'username' => $user->username,
      'password' => '123456',
    ];

    $this
      ->json('POST', $this->routes['register/seller'], $request)
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
      ->assertJsonStructure([
        'message'
      ])
      ->assertJson([
        'message' => 'The email has already been taken.',
      ]);
  }

  /**
   * A user can login successfully.
   *
   * @return void
   */
  public function testAUserCanLoginSuccessfully()
  {
    $user = User::factory(['password' => Hash::make('123456')])->create();

    $request = [
      'email' => $user->email,
      'password' => '123456',
    ];

    $this
      ->json('POST', $this->routes['login'], $request)
      ->assertStatus(Response::HTTP_OK)
      ->assertJsonStructure([
        'accessToken'
      ]);
  }

  /**
   * A user can not login without required input.
   *
   * @return void
   */
  public function testAUserCanNotLoginWithoutRequiredInput()
  {
    $this
      ->json('POST', $this->routes['login'], [])
      ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
      ->assertJsonValidationErrorFor('email')
      ->assertJsonValidationErrorFor('password');
  }

  /**
   * A user can not login with invalid credentials.
   *
   * @return void
   */
  public function testAUserCanNotLoginWithInvalidCredentials()
  {
    $user = User::factory(['password' => Hash::make('123456')])->create();

    $request = [
      'email' => $user->email,
      'password' => $this->faker->password(),
    ];

    $this
      ->json('POST', $this->routes['login'], $request)
      ->assertStatus(Response::HTTP_UNAUTHORIZED)
      ->assertJson([
        "message" => "Invalid credentials",
      ]);
  }
}
