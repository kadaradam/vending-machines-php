<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\BuyerProductResource;
use App\Http\Resources\BuyerProductCollection;

class BuyerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
