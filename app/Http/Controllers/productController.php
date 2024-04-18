<?php

namespace App\Http\Controllers;


use App\Models\Product;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;
use Exception;
use Illuminate\Http\Request;

class productController extends Controller
{

    public function index()
    {
        try{
            $products = Product::with(['user', 'brand','category'])->get();
            return response()->json($products);
        }catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

    public function personal()
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $products = Product::with(['user', 'brand', 'category'])->where('user_id', $payload['user_id'])->get();
            return response()->json($products);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

    public function byCategory(string $id)
    {
        $products = Product::with(['user', 'brand', 'category'])->where('category_id', $id)->get();
        return response()->json($products);
    }

    public function create(Request $req)
    {
        try {
            $validatedData = $req->validate([
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
                'stock' => 'required|numeric',
                'price' => 'required|numeric',
                'description' => 'required|string|max:255',
                'category_id' => 'required|numeric',
                'brand_id' => 'required|numeric',
            ]);
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $product = Product::create($validatedData);

            $product->user_id = $payload['user_id'];
            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $product->id . '.jpg';

                $image->storeAs('images/product', $imageName);
            }
            $product->image = 'http://jul2nd.ddns.net/storage/images/product/' . $product->id . '.jpg';
            $product->save();

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

    public function show(string $id)
    {
        $product = Product::with(['user', 'brand', 'category'])->find($id);

        if ($product) {
            return response()->json([
                'success' => true,
                'product' => $product,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'product not found',
            ]);
        }
    }

    public function edit(Request $req, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        $product->fill($req->only([
            'name',
            'price',
            'description',
            'image',
            'stock',
            'brand_id',
        ]));

        $product->save();

        return response()->json(['message' => 'Product updated successfully', 'data' => $product], 200);
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $product_id)
    {
        try{
            $product = Product::find($product_id);
            if($product) {
                $product->delete();
                response()->json(['success' => 'Product deleted successfully !', 200]);
            }
            else{
                response()->json(['success' => 'success', 404]);
            }
        }catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }
}
