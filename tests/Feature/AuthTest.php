<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
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

    $response = $this
      ->json('POST', $this->routes['register/seller'], $request)
      ->assertStatus(Response::HTTP_CREATED)
      ->assertJsonStructure([
        'id',
        'username',
        'email',
        'updated_at',
        'created_at',
        'role'
      ])
      ->assertJson([
        'email' => $request['email'],
        'username' => $request['username'],
        'role' => User::ROLES['SELLER']
      ]);

    $this->assertInstanceOf(UserResource::class, $response->getOriginalContent());
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

    $response = $this
      ->json('POST', $this->routes['register/buyer'], $request)
      ->assertStatus(Response::HTTP_CREATED)
      ->assertJsonStructure([
        'id',
        'username',
        'email',
        'updated_at',
        'created_at',
        'role'
      ])
      ->assertJson([
        'email' => $request['email'],
        'username' => $request['username'],
        'role' => User::ROLES['BUYER']
      ]);

    $this->assertInstanceOf(UserResource::class, $response->getOriginalContent());
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
      ->assertJsonValidationErrorFor('email')
      ->assertJson([
        'errors' => [
          'email' => [
            'The email has already been taken.',
          ],
        ],
      ]);
  }
}
