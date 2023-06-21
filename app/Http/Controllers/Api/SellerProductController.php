<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProductFilter;
use App\Transformers\ProductLocaleTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSellerProductRequest;
use App\Http\Requests\UpdateSellerProductRequest;
use App\Models\Product;
use App\Http\Resources\SellerProductResource;
use App\Http\Resources\SellerProductCollection;
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
                ['seller_id', '=', $user->id],
            ]);

            return new SellerProductCollection(
                $products->paginate()->appends($request->query())
            );
        }

        return new SellerProductCollection(
            Product::where('seller_id', '=', $user->id)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSellerProductRequest $request)
    {
        $user = request()->user();

        $transformer = new ProductLocaleTransformer();
        $transformedBody = $transformer->transform($request);

        $product = Product::create([
            'seller_id' => $user->id,
            ...$transformedBody,
        ]);

        return (new SellerProductResource($product))
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = request()->user();
        $product = Product::where([
            ['id', '=', $id],
            ['seller_id', '=', $user->id],
        ])->first();

        if (!$product) {
            return response()->json(
                [
                    'message' => 'Not Found!',
                ],
                HttpResponse::HTTP_NOT_FOUND
            );
        }

        return new SellerProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSellerProductRequest $request, string $id)
    {
        $user = request()->user();
        $transformer = new ProductLocaleTransformer();
        $transformedBody = $transformer->transform($request);

        Product::where([
            ['id', "=", $id],
            ['seller_id', "=", $user->id]
        ])->first()->update($transformedBody);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = request()->user();
        $product = Product::where([
            ['id', '=', $id],
            ['seller_id', '=', $user->id],
        ])->first();

        if (!$product) {
            return response()->json(
                [
                    'message' => 'Not Found!',
                ],
                HttpResponse::HTTP_NOT_FOUND
            );
        }

        $product->delete();
    }
}
