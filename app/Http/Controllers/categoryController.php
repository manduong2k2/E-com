<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class categoryController extends Controller
{
    public function index()
    {
        try{
            $categories = Category::with([])->get();
            return response()->json($categories);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function create(Request $req)
    {
        try{
            $validatedData = $req->validate([
                'name' => 'required|string|max:255',             
            ]);

            $category = Category::create($validatedData);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        }
        catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function show(string $id)
    {
        try {
            $category = Category::with(['products'])->find($id);

            if ($category) {
                return response()->json([
                    'success' => true,
                    'category' => $category,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'category not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function edit(Request $req,string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Thể loại không tồn tại'], 404);
        }

        $category->fill($req->only([
            'name',
        ]));

        $category->save();

        return response()->json(['message' => 'Category updated successfully', 'data' => $category], 200);
    }
    public function destroy(string $id)
    {
        try{
            $category = Category::find($id);
            if($category) $category->delete();
            response()->json(['success' => 'success', 200]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }

}
