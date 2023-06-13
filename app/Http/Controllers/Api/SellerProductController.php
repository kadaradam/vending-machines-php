<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSellerProductRequest;
use App\Http\Requests\UpdateProductRequest;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSellerProductRequest $request)
    {
        $user = $request->user();
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
    public function show($id)
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
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
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
