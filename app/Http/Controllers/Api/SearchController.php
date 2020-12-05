<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromoProduct;
use App\Models\Product;
use  App\Http\Resources\Product\ProductCollection;

class SearchController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->searchType === 'searchBar') {
            $search = $request->searchQuery;
            $products = Product::with('categories')->where('name', $search)->limit(20)->get();
            // $products = Product::with('categories')->where('name', 'like', '%'. $search . '%')->limit(20)->get();
            return  response()->json(['products' => new ProductCollection($products), 'searchQuery' => $search]);
            ;
        } elseif ($request->searchType === 'homeSearch') {
            $promo = PromoProduct::where('slug', $request->slug)->first();
            // if (!empty($promo)) {
            return response()->json(['results' => new ProductCollection($promo->items), 'searchQuery' => $request->slug]);
            // }
        }
    }
}
