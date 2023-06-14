<?php

namespace Tests\Feature;

use App\Http\Resources\SellerProductCollection;
use App\Http\Resources\SellerProductResource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Error;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class SellerProductTest extends TestCase
{
    use RefreshDatabase;

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

    public function testASellerCanListItsProducts(): void
    {
        $products = Product::factory([
            "seller_id" => $this->user->id,
        ])
            ->count(3)
            ->create();

        $request = Request::capture();
        $expectedResponse = SellerProductResource::collection($products)->toArray($request);

        $this
            ->json('GET', $this->routes['index'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'cost',
                        'wallet',
                        'sellerId'
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                ]
            ])
            ->assertJson([
                'data' => $expectedResponse,
            ]);
    }
}
