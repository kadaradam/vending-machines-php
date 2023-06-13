<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\BuyerProductResource;
use App\Http\Resources\BuyerProductCollection;
use App\Services\ProductQuery;
use Illuminate\Http\Request;

class BuyerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new ProductQuery();
        $queryItems = $filter->transform($request);

        if (count($queryItems) > 0) {
            return new BuyerProductCollection(Product::where($queryItems)->paginate());
        }

        return new BuyerProductCollection(Product::paginate());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new BuyerProductResource(Product::find($id));
    }
}
