<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class userController extends Controller
{
    public function signup(Request $req)
    {
        try {
            $validatedData = $req->validate([
                'username' => 'required|string|max:255',
                'full_name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
                'email' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);

            $user = User::create($validatedData);

            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $user->username . '.jpg';

                $image->storeAs('images/user', $imageName);
            }
            $user->image = 'jul2nd.ddns.net/storage/images/user/' . $user->username . '.jpg';
            $user->save();

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function login()
    {
        try {
            $credentials = request(['email', 'password']);
            $user = User::where('email', $credentials['email'])->first();
            $hashedPassword = $user->password;
            if (Hash::check($credentials['password'], $hashedPassword)) {
                $token = JWTAuth::fromUser($user);
                return response()->json([
                    'success' => true,
                    'token' => $token,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
}
