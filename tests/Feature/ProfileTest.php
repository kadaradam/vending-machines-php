<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
  use RefreshDatabase;
  use WithFaker;

  /**
   * @var array
   */
  private $routes;

  /**
   * @var User
   */
  private $user;

  /**
   * Setup the test.
   *
   * @return void
   */
  public function setUp(): void
  {
    parent::setUp();

    $this->routes = [
      'me' => '/api/user/me',
    ];

    $this->user = User::factory()->create();

    Sanctum::actingAs($this->user);
  }

  /**
   * A user can get his profile.
   *
   * @return void
   */
  public function testAUserCanGetHisProfile()
  {
    $this
      ->json('GET', $this->routes['me'])
      ->assertStatus(Response::HTTP_OK)
      ->assertJsonStructure([
        'data' => [
          'id',
          'username',
          'email',
          'role',
          'createdAt',
          'updatedAt'
        ]
      ])
      ->assertJson(
        UserResource::make($this->user)->response()->getData(true)
      );
  }
}
