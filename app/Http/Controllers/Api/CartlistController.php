<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cartlist;
use Illuminate\Support\Facades\Auth;
use App\Models\Shipping;
use App\Models\Product;
use App\Http\Resources\Cartlist\CartlistCollection;
use  App\Http\Resources\Cartlist\Cartlist as CartlistResource;
use App\Http\Resources\Shipping\Shipping as ShippingResource;
use App\User;

class CartlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth = Auth::user();
        if ($auth) {
            $cartlist = Cartlist::where(['userId' => $auth->id, 'processed' => 0])->latest()->get();
            //
            $shipping = Shipping::where('user_id', $auth->id)->latest()->first();
            if (count($cartlist) > 0) {
                $update = false;
                $total_amount = $cartlist->sum('TotalPrice');
                if (!empty($shipping)) {
                    $update = true;
                } else {
                    $shipping = new Shipping();
                    $update = false;
                }
                $shipping->total_amount = $total_amount;
                $shipping->subtotal_amount = $total_amount;
                $shipping->shipping_amount = 0;
                $shipping->user_id = $auth->id;
                if ($update) {
                    $shipping->update();
                } else {
                    $shipping->save();
                }
            }

            return new CartlistCollection($cartlist);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request->id);
        // //save cart
        $cart = $this->add($product, $product->id);

        return response()->json('s');

        // return  new CartlistResource($cart);
    }

    public function StoreCart(Request $request)
    {
        if ($request->localCart) {
            $user = User::where('email', $request->email)->first();
            $cartlist = $request->cart;
            $list = [];
            foreach ($cartlist as $c) {
                Cartlist::where(['SKU' => $c['SKU'], 'processed' => 0])->delete();
            }

            foreach ($cartlist as $c) {
                $cart = new Cartlist();
                $cart->ProductName = $c['ProductName'];
                $cart->Media = json_encode($c['Media']);
                $cart->Description = $c['Description'];
                $cart->Price = $c['Price'];
                $cart->TotalPrice = $c['TotalPrice'];
                $cart->SKU = $c['SKU'];
                $cart->ProductThumb = $c['ProductThumb'];
                $cart->Quantity = $c['Quantity'];
                $cart->userId =  $user->id;
                $cart->processed =  0;
                $cart->save();
            }

            return  new CartlistResource($cart);
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
        $cartlist = Cartlist::find($id);
        $cartlist->Quantity = $request->Quantity;
        $cartlist->TotalPrice = $request->SubTotal;
        $cartlist->update();
        $auth = Auth::user();
        $cart = Cartlist::where('userId', $auth->id)->latest();
        if ($auth) {
            $shipping = Shipping::where('user_id', $auth->id)->latest()->first();
            $total_item = $cart->sum('TotalPrice');
            if ($shipping) {
                $shipping->total_amount = $total_item;
                $shipping->subtotal_amount = $total_item;
                $shipping->user_id = $auth->id;
                $shipping->update();
            } else {
                $shipping = new Shipping();
                $shipping->total_amount = $total_item;
                $shipping->subtotal_amount = $total_item;
                $shipping->user_id = $auth->id;
                $shipping->save();
            }
        }

        return  new ShippingResource($shipping);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auth = Auth::user();
        $cartlist = Cartlist::findOrFail($id);
        $cartlist->delete();
        $shipping = Shipping::where('user_id', $auth->id)->latest()->first();
        if (!empty($shipping)) {
            $total_item = ($shipping->total_amount - $cartlist->TotalPrice);
            $shipping->total_amount = $total_item;
            $shipping->subtotal_amount = $total_item;
            $shipping->user_id = $auth->id;
            $shipping->update();
        }

        return response()->json(['cartlist' => new CartlistResource($cartlist), 'shipping' => new ShippingResource($shipping)]);
    }

    public function add($item, $id)
    {
        $storedItem = ['totalQty' => 0, 'totalPrice' => 0, 'qty' => 0, 'price' => $item->price, 'item' => $item, 'media' => json_encode($item->media)];
        $auth = Auth::user();

        $storedItem['qty']++;
        $storedItem['price'] = $item->price * $storedItem['qty'];
        $storedItem['totalQty']++;
        $storedItem['totalPrice'] += $item->price;
        //check previous cart
        $cart = Cartlist::where(['sku' => $item->sku, 'userId' => $auth->id, 'processed' => 0])->first();
        if ($cart) {
            try {
                $qyt = $cart->Quantity + 1;
                $cart->Quantity = $qyt;
                $cart->TotalPrice = $cart->Price  * $qyt;
                $cart->update();
            } catch (\Throwable $th) {
                return;
            }
        } else {
            $cart = new Cartlist();
            $cart->ProductName = $item->name;
            $cart->Media = $storedItem['media'];
            $cart->Description = $item->description;
            $cart->Price = $storedItem['price'];
            $cart->TotalPrice = $storedItem['totalPrice'];
            $cart->SKU = $item->sku;
            $cart->ProductThumb = $item->product_thumbnail;
            $cart->Quantity = $storedItem['totalQty'];
            $cart->processed =  0;
            $cart->userId =  $auth->id;
            $cart->save();
        }

        return $cart;
    }
}
