<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        $token = $request->bearerToken();
        $token = isset($token) ? $token : auth()->user()->tokens();
        if(!$token){
            return response()->json(['user' => '','status' => false,'message' => 'Invalid Token provided.']);
        }else{
            return $next($request);
        }
    }

}
