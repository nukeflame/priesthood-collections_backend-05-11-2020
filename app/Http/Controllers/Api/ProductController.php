<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Str;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Controllers\Traits\FileUploadTrait;
use App\Http\Resources\Product\Product as ProductResource;

class ProductController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products  = Product::orderBy('created_at', 'desc')->get();
        return new ProductCollection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $r = $this->saveFiles($request);
        //create product
        if (!empty($r->all())) {
            $category = explode(',', $r->category);
            $p = new Product();
            $p->name = $r->productName;
            $p->price = !empty($r->price) ?  $this->moneyDecode($r->price) : 0.00;
            $p->compare_price = !empty($r->comparePrice) ?  $this->moneyDecode($r->comparePrice) : 0.00;
            $p->shipping_price = !empty($r->shippingPrice) ?  $this->moneyDecode($r->shippingPrice) : 0.00;
            $p->sku = $r->sku;
            $p->specifications = $r->specifications;
            $p->brand_id = (int) $r->brandId === -1 ? null : (int) $r->brandId ;
            $p->description = $r->description;
            $p->product_thumbnail = $r->productThumb !== null ? $r->productThumb : null;
            $p->save();
            $p->categories()->attach($category);
            // insert files
            if ($r->productThumb !== null) {
                $p->media()->attach($r->productThumbId);
            }
            if ($r->attachedFiles) {
                $p->media()->attach($r->attachedFiles);
            }
            // stock create
            if (!empty($p)) {
                $s = new Stock();
                $s->sku	= $p->sku;
                $s->stock_status = $r->stockStatus;
                $s->stock_quantity = $r->stockQnty;
                $s->initial_stock = $r->stockQnty;
                $s->product_id = $p->id;
                $s->save();
            }
        }
        
        return new ProductResource($p);
    }

    public function moneyDecode($m)
    {
        $n = explode(',', $m);
        $k = 0;
        if (count($n) === 1) {
            $k = $n[0];
        } elseif (count($n) === 2) {
            $k = $n[0].$n[1];
        } elseif (count($n) === 3) {
            $k = $n[0].$n[1].$n[2];
        }
        return $k;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return  response()->json($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return new ProductResource($product);
    }

    /**
     * Sort the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        return  response()->json($request->all());
    }
}
