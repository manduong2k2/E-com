<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;

class brandController extends Controller
{
    public function create(Request $req)
    {
        try{
            $validatedData = $req->validate([
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',               
                'description' => 'required|string|max:255',
            ]);

            $brand = Brand::create($validatedData);

            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $brand->id.'.jpg';
                $image->storeAs('images/brands', $imageName);
            }
            $brand->image='jul2nd.ddns.net/storage/images/brand/'.$brand->id.'.jpg';
            $brand->save();

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $brand,
            ], 201);
        }
        catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
}
