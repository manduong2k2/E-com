<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;
use Exception;
use Illuminate\Http\Request;


class cartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $carts = Cart::with(['product'])->where('user_id', $payload['user_id'])->get();
            return response()->json($carts);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $product_id)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $cart = Cart::where('user_id', $payload['user_id'])->where('product_id', $product_id)->first();

            if (!$cart) {
                $cart = new Cart();
                $cart->user_id = $payload['user_id'];
                $cart->product_id = $product_id;
                $cart->quantity = 1;
                $cart->save();
            }else{
                $cart->quantity+=1;
                $cart->save();
            }
            response()->json(['success' => 'success', 200]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

    public function update(Request $req,string $product_id)
    {
        try{
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            $cart = Cart::where('user_id', $payload['user_id'])->where('product_id', $product_id)->first();
            if($cart){
                $cart->quantity=$req->number;
                $cart->save();
            }
            response()->json(['success' => 'success', 200]);
        }catch(Exception $e){
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $product_id)
    {
        try{
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            $cart = Cart::where('user_id', $payload['user_id'])->where('product_id', $product_id)->first();
            if($cart) $cart->delete();
            response()->json(['success' => 'success', 200]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

    public function destroyAll()
    {
        try{
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            $carts = Cart::where('user_id', $payload['user_id'])->get();
            if ($carts->isNotEmpty()) {
                $carts->each->delete(); 
            }
            return response()->json(['success' => 'success'], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }
}
