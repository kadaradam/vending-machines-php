<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\BuyerProductResource;
use App\Http\Resources\BuyerProductCollection;
use App\Filters\ProductFilter;
use Illuminate\Http\Request;

class BuyerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new ProductFilter();
        $queryItems = $filter->transform($request);
        $populateSeller = $request->query('populateSeller');

        $products = Product::where($queryItems);

        if ($populateSeller) {
            $products = $products->with('seller');
        }

        return new BuyerProductCollection(
            $products->paginate()->appends($request->query())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new BuyerProductResource(Product::find($id));
    }
}
