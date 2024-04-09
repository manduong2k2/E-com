<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class categoryController extends Controller
{
    public function create(Request $req)
    {
        try{
            $validatedData = $req->validate([
                'name' => 'required|string|max:255',             
            ]);

            $category = Category::create($validatedData);

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $category,
            ], 201);
        }
        catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
}
