<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Jobs\SendEmail;
use App\Mail\RecoverMail;
use App\Mail\UserNotification;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
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
                'address' => 'string|max:255',
                'password' => 'required|string|max:255',
            ]);

            $user = User::create($validatedData);

            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $user->username . '.jpg';

                $image->storeAs('public/images/user', $imageName);
            }
            $user->image = 'http://localhost/storage/images/user/' . $user->username . '.jpg';
            $user->save();
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
            if(!$user)
            $user = User::where('username', $credentials['email'])->first();
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

            $user = User::with(['roles'])->find($payload['user_id']);

            if (!$user) {
                return response()->json(['message' => 'Người dùng không tồn tại'], 404);
            }

            $user->fill($req->only([
                'username',
                'fullname',
                'email',
                'address',
            ]));

            if ($req->hasFile('image')) {
                $image = $req->file('image');
                $imageName = $user->username . '.jpg';

                $image->storeAs('public/images/user', $imageName);
                $user->image = 'http://localhost/storage/images/user/' . $user->username . '.jpg';
            }

            $user->save();

            return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
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

    public function changePassword(Request $req)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            $credentials = request(['oldPassword', 'newPassword']);
            $user = User::find($payload['user_id']);
            
            if (!Hash::check($credentials['oldPassword'], $user->password)) {
                return response()->json(['message' => 'Mật khẩu cũ không đúng !'], 404);
            }

            $user->password = Hash::make($credentials['newPassword']);
            $user->save();
            return response()->json(['message' => 'Category updated successfully', 'data' => $user], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->__toString(),
            ], 500);
        }
    }

    public function sendEmail(string $email)
    {
        try {
            $user = User::where('email',$email)->first();
            $code = Str::random(6);
            if ($user) {
                Queue::push(new SendEmail(new RecoverMail($user,$code)));

                $user->password = Hash::make($code);
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Send email to user success !',
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
    public function products()
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $products= Product::with('brand','category')->where('user_id',$payload['user_id'])->get();

            if ($products->isNotEmpty()) {
                return response()->json($products);
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
    public function destroy(string $product_id)
    {
        try{
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            
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
