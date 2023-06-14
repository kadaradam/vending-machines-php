<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProductFilter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSellerProductRequest;
use App\Http\Requests\UpdateSellerProductRequest;
use App\Models\Product;
use App\Http\Resources\SellerProductResource;
use App\Http\Resources\SellerProductCollection;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class SellerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = new ProductFilter();
        $queryItems = $filter->transform($request);

        if (count($queryItems) > 0) {
            $products = Product::where([
                ...$queryItems,
                ['seller_id', "=", $user->id]
            ])->paginate();

            return new SellerProductCollection(
                $products->appends($request->query())
            );
        }

        return new SellerProductCollection(
            Product::where('seller_id', "=", $user->id)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSellerProductRequest $request)
    {
        $user = request()->user();
        $name = $request->name;
        $cost = $request->cost;

        return Response::json(
            new SellerProductResource(Product::create([
                'seller_id' => $user->id,
                'name' => $name,
                'cost' => $cost,
            ])),
            HttpResponse::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = request()->user();
        $product = Product::where([
            ['id', "=", $id],
            ['seller_id', "=", $user->id]
        ])->first();

        if (!$product) {
            return response()->json([
                'message' => 'Not Found!'
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        return new SellerProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSellerProductRequest $request, string $id)
    {
        $user = request()->user();

        Product::where([
            ['id', "=", $id],
            ['seller_id', "=", $user->id]
        ])->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = request()->user();
        $product = Product::where([
            ['id', "=", $id],
            ['seller_id', "=", $user->id]
        ])->first();

        if (!$product) {
            return response()->json([
                'message' => 'Not Found!'
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        $product->delete();
    }
}
