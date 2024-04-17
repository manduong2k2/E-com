<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;
use Exception;

class orderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $orders = Order::with(['product'])->where('user_id', $payload['user_id']);
            return response()->json($orders);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 404);
        }
        
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $req)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $order = new Order();
            $order->user_id = $payload['user_id'];
            $order->date = Carbon::now();
            $order->save();
            $product_ids = $req->input('product_ids', []);

            if($product_ids->isNotEmpty()){
                foreach ($product_ids as $product_id) {
                    $cart = Cart::with(['product'])->where('user_id', $payload['user_id'])->where('product_id', $product_id)->first();
                    if ($cart) {
                        $product = $cart->product;
                        if ($product) {
                            $Item = new Item();
                            $Item->order_id = $order->id;
                            $Item->product_id = $product_id;
                            $Item->cost = $product->price * $cart->quantity; 
                            $Item->quantity = $cart->quantity;
                            $Item->save();
                            
                            $cart->delete();
                        }
                    }
                }
            }
            else{
                return response()->json([
                    'message' => 'Cart is empty',
                ], 401);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
