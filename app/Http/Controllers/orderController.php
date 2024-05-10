<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Mail\OrderPlacedMail;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

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

            $orders = Order::with(['items.product'])->where('user_id', $payload['user_id'])->get();
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
            $user = User::find($payload['user_id']);
            $carts = Cart::with(['product'])->where('user_id', $payload['user_id'])->get();

            if ($carts->isNotEmpty()) {
                foreach ($carts as $cart) {
                    $product = $cart->product;
                    if ($product) {
                        $item = new Item();
                        $item->order_id = $order->id;
                        $item->product_id = $cart->product_id;
                        $item->cost = $product->price * $cart->quantity;
                        $item->quantity = $cart->quantity;
                        $product->stock -= $cart->quantity;
                        $product->save();
                        $order->total+=$item->cost;
                        $item->save();
                        $cart->delete();
                    }
                }
                $order->save();
                $orderMail=new OrderPlacedMail($order);
                $sendEmail=new SendEmail($orderMail);
                //Queue::push($sendEmail); //work
                SendEmail::dispatch($orderMail)->onQueue('emails'); //work
                return response()->json([
                    'message' => 'Order placed successfully !',
                ], 200);
            }
            else{
                return response()->json([
                    'message' => 'Cart is empty',
                ], 402);
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
