<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class productController extends Controller
{
    
    public function index()
    {
        $products = Product::with(['user','brand','category'])->get();
        return response()->json($products);
    }

    public function create(Request $req)
    {
        try{
            $validatedData = $req->validate([
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
                'stock' => 'required|numeric',
                'description' => 'required|string|max:255',
                'brand_id' => 'required|numeric',
                'category_id' => 'required|numeric',
                'user_id' => 'required|numeric'
            ]);

            $product = Product::create($validatedData);

            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $product->id.'.jpg';
    
                $image->storeAs('images/product', $imageName);
            }
            $product->image='jul2nd.ddns.net/storage/images/product/'.$product->id.'.jpg';
            $product->save();

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        }
        catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        
    }

    public function show(string $id)
    {
        
    }

    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        
    }

    public function destroy(string $id)
    {
        
    }
}
