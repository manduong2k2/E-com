<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;

class brandController extends Controller
{
    public function index()
    {
        try{
            $brands = Brand::with([])->get();
            return response()->json($brands);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function create(Request $req)
    {
        try {
            $validatedData = $req->validate([
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
                'description' => 'required|string|max:255',
            ]);

            $brand = Brand::create($validatedData);

            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $brand->id . '.jpg';
                $image->storeAs('images/brand', $imageName);
            }
            $brand->image = 'http://jul2nd.ddns.net/storage/images/brand/' . $brand->id . '.jpg';
            $brand->save();

            return response()->json([
                'message' => 'Brand created successfully',
                'data' => $brand,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $brand = Brand::with(['products'])->find($id);

            if ($brand) {
                return response()->json([
                    'success' => true,
                    'brand' => $brand,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'brand not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }

    public function edit(Request $req, string $id)
    {
        try {
            $brand = Brand::find($id);

            if (!$brand) {
                return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
            }

            $brand->fill($req->only([
                'name',
                'description',
                'image',
            ]));
            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $brand->id . '.jpg';
                $image->storeAs('images/brand', $imageName);
            }
            $brand->image = 'http://jul2nd.ddns.net/storage/images/brand/' . $brand->id . '.jpg';

            $brand->save();

            return response()->json(['message' => 'Brand updated successfully', 'request' => $req->all()], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function destroy(string $id)
    {
        try{
            $brand = Brand::find($id);
            if($brand) $brand->delete();
            response()->json(['success' => 'success', 200]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->__toString(),
            ], 501);
        }
    }
}
