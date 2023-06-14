<?php

namespace Tests\Feature;

use App\Http\Resources\SellerProductResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Error;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;

class SellerProductTest extends TestCase
{
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
            'index' => '/api/products/seller',
            'store' => '/api/products/seller',
            'show' => '/api/products/seller/1',
            'update' => '/api/products/seller/1',
            'destroy' => '/api/products/seller/1',
        ];

        $this->user = User::factory(['role' => User::ROLES['SELLER']])->create();

        Sanctum::actingAs($this->user);
    }

    public function testASellerCanCreateProduct(): void
    {
        $request = [
            'name' => 'Coca Cola',
            'cost' => 2,
        ];

        $response = $this
            ->json('POST', $this->routes['store'], $request)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'name',
                'cost',
                'wallet',
                'sellerId'
            ])
            ->assertJson([
                'name' => $request['name'],
                'cost' => $request['cost'],
                'wallet' => null,
                'sellerId' => $this->user->id
            ]);

        $this->assertInstanceOf(SellerProductResource::class, $response->getOriginalContent());
    }
}
