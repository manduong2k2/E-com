<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $user = User::with(['roles'])->where('email',$payload['email'])->first();

            if ($user) {
                $adminRole = $user->roles->where('id', 2)->first();
                if($adminRole){
                    return $next($request);
                }else{ 
                    return response()->json(['error' => 'Unauthorized','roles'=>$user->roles], 401);
                }
            }
        }catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }catch(TokenExpiredException $e){
            return response()->json(['error' => 'Session expired'], 401);
        }catch(Exception $e){
            return response()->json(['error' => $e->__toString()],501);
        }
        // Unauthorized response
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}
