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
    $response = $this
      ->json('GET', $this->routes['me'])
      ->assertStatus(Response::HTTP_OK)
      ->assertJsonStructure([
        'id',
        'username',
        'email',
        'created_at',
        'updated_at'
      ])
      ->assertJson([
        'id' => $this->user->id,
        'username' => $this->user->username,
        'email' => $this->user->email,
        'created_at' => $this->user->created_at->toJSON(),
        'updated_at' => $this->user->updated_at->toJSON(),
      ]);

    $this->assertInstanceOf(UserResource::class, $response->getOriginalContent());
  }
}
