<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class userController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['roles'])->get();
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString()
            ], 502);
        }
    }
    public function signup(Request $req)
    {
        try {
            $validatedData = $req->validate([
                'username' => 'required|string|max:255',
                'fullname' => 'required|string|max:255',
                'image' => 'required|file',
                'email' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);

            $user = User::create($validatedData);

            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $user->username . '.jpg';

                $image->storeAs('images/user', $imageName);
            }
            $user->image = 'http://jul2nd.ddns.net/storage/images/user/' . $user->username . '.jpg';
            $user->save();

            $user->roles()->attach(1);

            if ($user->email == 'manduong2k2@gmail.com')
                $user->roles()->attach(2);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
                'req' => $req->all()
            ], 502);
        }
    }
    public function login()
    {
        try {
            $credentials = request(['email', 'password']);
            $user = User::where('email', $credentials['email'])->first();
            if ($user) {
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
    public function edit(Request $req)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $user = User::with(['roles'])->find($payload['id']);

            if (!$user) {
                return response()->json(['message' => 'Người dùng không tồn tại'], 404);
            }

            $user->fill($req->only([
                'username',
                'fullname',
                'email',
                'image'
            ]));

            $user->save();

            return response()->json(['message' => 'Category updated successfully', 'data' => $user], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function grant(string $id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->roles()->attach(2);
                return response()->json(['message' => 'Admin role granted !'], 200);
            } else
                return response()->json(['message' => 'User not found !'], 405);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function revoke(string $id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->roles()->detach(2);
                return response()->json(['message' => 'Role revoked successfully!'], 200);
            } else
                return response()->json(['message' => 'User not found !'], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }
    public function show()
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $user = User::with(['roles'])->find($payload['user_id']);

            if ($user) {
                return response()->json([
                    'success' => true,
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 502);
        }
    }
}
