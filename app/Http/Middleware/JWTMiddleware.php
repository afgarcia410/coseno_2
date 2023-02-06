<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = 'muy_privada'; //Clave para cifrar y descifrar el jwt
        $header = $request->header('Authorization');
        $token = \Str::substr($header, 7);
        if($token){
            try{
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                $user = $decoded->user;
                return $next($request);
            }catch(\Exception $e){
                return response()->json(['message' => 'Not authorized'], 401);
            }
        }
        return response()->json(['message' => 'Not authorized'], 401);
    }
}
