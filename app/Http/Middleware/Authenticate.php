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

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            $user = User::where('email',$payload['email'])->first();

            if ($user) {
                return $next($request);
            }
        }catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }catch(TokenExpiredException $e){
            return response()->json(['error' => 'Session expired'], 401);
        }catch(Exception $e){
            return response()->json(['error' => $e->__toString()],500);
        }
        // Unauthorized response
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}