<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PromoProduct;
use App\Http\Resources\Post\Post as PostResource;
use App\Http\Resources\Post\PromoProductCollection;
use App\Http\Requests\Product\PromoProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;

class PromoProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promo = PromoProduct::latest()->get();
        return new PromoProductCollection($promo);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PromoProductRequest $request)
    {
        $post = Post::find($request->postId);
        if ($post) {
            $promo = new PromoProduct();
            if ($request->hasFile('promoFile')) {
                $storage_path = 'public/assets/images';
                $path = $request->file('promoFile')->store($storage_path);
                $pathstorage = Storage::url($path);
                $url = env('APP_URL') . $pathstorage;
                $promo->product_avatar = $url;
            }
            $exploded = explode(',', $request->products);
            $products = Product::find($exploded);
            if (count($products) < 0) {
                return;
            }
            $promo->product_name = $request->infoName;
            $promo->product_price = $request->infoPrice;
            $promo->product_info = $request->pInfo;
            $promo->slug = Str::slug($request->infoName);
            $promo->tags = $request->infoTags;
            $promo->post_id = $post->id;
            $promo->created_at = now();
            $promo->save();
            //
            $post->status = 'publish';
            $post->update();
            $promo->items()->attach($exploded);

            return new PostResource($post);
        }
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
        return response()->json($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = PromoProduct::find($id);
        if ($product) {
            $product->delete();
            return new PostResource($product->post);
        }
    }
}
