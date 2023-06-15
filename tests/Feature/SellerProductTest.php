<?php

namespace Tests\Feature;

use App\Http\Resources\SellerProductResource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class SellerProductTest extends TestCase
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
            'index' => '/api/products/seller',
            'index-filter-by-name' => '/api/products/seller?name[eq]=Cola',
            'index-filter-by-cost' => '/api/products/seller?cost[gt]=3',
            'index-filter-by-seller' => '/api/products/seller?sellerId[eq]=-1',
            'store' => '/api/products/seller',
            'show' => '/api/products/seller',
            'update' => '/api/products/seller',
            'destroy' => '/api/products/seller',
            'buyer-show' => '/api/products/buyer',
            'buyer-index' => '/api/products/buyer',
        ];

        $this->user = User::factory(['role' => User::ROLES['SELLER']])->create();

        Sanctum::actingAs($this->user);
    }

    public function tearDown(): void
    {
        // Clear only the data created during the test
        $this->refreshDatabase();

        parent::tearDown();
    }

    public function testASellerCanListItsProducts(): void
    {
        $products = Product::factory([
            'seller_id' => $this->user->id,
        ])
            ->count(3)
            ->create();
        Product::factory([
            'seller_id' => -1,
            'name' => 'Fanta',
            'cost' => 3
        ])->create();

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

    public function testASellerCanFilterProducts(): void
    {
        $product1 = Product::factory([
            'seller_id' => $this->user->id,
            'name' => 'Cola',
            'cost' => 2
        ])->create();
        Product::factory([
            'seller_id' => -1,
            'name' => 'Fanta',
            'cost' => 4
        ])->create();
        $product3 = Product::factory([
            'seller_id' => $this->user->id,
            'name' => 'Beer',
            'cost' => 5
        ])->create();

        $request = Request::capture();
        $transformedProducts = SellerProductResource::collection(
            [$product1, $product3]
        )->toArray($request);

        $this
            ->json('GET', $this->routes['index-filter-by-name'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    $transformedProducts[0]
                ],
            ]);

        $this
            ->json('GET', $this->routes['index-filter-by-cost'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    $transformedProducts[1]
                ],
            ]);

        $this
            ->json('GET', $this->routes['index-filter-by-seller'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(0, 'data');
    }

    public function testASellerCanViewAProduct(): void
    {
        $product = Product::factory([
            'seller_id' => $this->user->id,
            'name' => 'Cola',
            'cost' => 2
        ])->create();

        $productId = $product->id;
        $route = "{$this->routes['show']}/{$productId}";

        $this
            ->json('GET', $route)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'cost',
                    'wallet',
                    'sellerId'
                ]
            ])
            ->assertJson(
                SellerProductResource::make($product)->response()->getData(true)
            );
    }

    public function testASellerCantViewOthersProduct(): void
    {
        $product = Product::factory([
            'seller_id' => 999,
            'name' => 'Cola',
            'cost' => 2
        ])->create();

        $productId = $product->id;
        $route = "{$this->routes['show']}/{$productId}";

        $this
            ->json('GET', $route)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Not Found!'
            ]);
    }

    public function testASellerCanCreateProduct(): void
    {
        $request = [
            'name' => 'Coca Cola',
            'cost' => 2,
        ];

        $resourceData = (object) [
            'id' => 1,
            'name' => $request['name'],
            'cost' => $request['cost'],
            'wallet' => null,
            'seller_id' => $this->user->id,
        ];
        $expectedResult = SellerProductResource::make($resourceData)->response()->getData(true);
        unset($expectedResult['data']['id']);

        $this
            ->json('POST', $this->routes['store'], $request)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'cost',
                    'wallet',
                    'sellerId'
                ]
            ])
            ->assertJson($expectedResult);
    }

    public function testASellerCanPatchProduct(): void
    {
        $product = Product::factory([
            'seller_id' => $this->user->id,
            'name' => 'Cola',
            'cost' => 2
        ])->create();
        $id = $product->id;

        $request = [
            'name' => $this->faker->jobTitle(),
        ];

        $this
            ->json('PATCH', "{$this->routes['update']}/{$id}", $request)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => $request['name']
        ]);
    }

    public function testASellerCanPutProduct(): void
    {
        $product = Product::factory([
            'seller_id' => $this->user->id,
            'name' => 'Cola',
            'cost' => 2
        ])->create();
        $id = $product->id;

        $request = [
            'name' => $this->faker->jobTitle(),
            'cost' => 100
        ];

        $this
            ->json('PUT', "{$this->routes['update']}/{$id}", $request)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => $request['name'],
            'cost' => $request['cost']
        ]);
    }

    public function testASellerCantPatchSellerId(): void
    {
        $userId = $this->user->id;
        $product = Product::factory([
            'seller_id' => $userId,
            'name' => 'Cola',
            'cost' => 2
        ])->create();
        $id = $product->id;
        $route = "{$this->routes['update']}/{$id}";

        $request = [
            'sellerId' => 999,
        ];

        $this
            ->json('PATCH', $route, $request)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('products', [
            'id' => $id,
            'seller_id' => $userId,
        ]);
    }

    public function testASellerCantDeleteOthersProduct(): void
    {
        $product = Product::factory([
            'seller_id' => 999,
            'name' => 'Cola',
            'cost' => 2
        ])->create();
        $id = $product->id;
        $route = "{$this->routes['destroy']}/{$id}";

        $this
            ->json('DELETE', $route)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Not Found!'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $id,
        ]);
    }

    public function testASellerCantListBuyerProducts(): void
    {
        $this
            ->json('GET', $this->routes['buyer-index'])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJson([
                'message' => 'Unauthorized',
            ]);
    }

    public function testASellerCantViewABuyerProduct(): void
    {
        $productId = 999;
        $route = "{$this->routes['buyer-show']}/{$productId}";

        $this
            ->json('GET', $route)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJson([
                'message' => 'Unauthorized',
            ]);
    }
}
