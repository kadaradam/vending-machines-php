<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSellerProductRequest;
use App\Http\Requests\UpdateSellerProductRequest;
use App\Models\Product;
use App\Http\Resources\SellerProductResource;
use App\Http\Resources\SellerProductCollection;

class SellerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return new SellerProductCollection(Product::where('seller_id', "=", $user->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSellerProductRequest $request)
    {
        $name = $request->name;
        $cost = $request->cost;

        return new SellerProductResource(Product::create([
            'seller_id' => 7,
            'name' => $name,
            'cost' => $cost,
        ]));
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
            ], 404);
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
            ], 404);
        }

        $product->delete();
    }
}
