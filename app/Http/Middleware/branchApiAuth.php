<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use JWTAuth;
use Exception;


use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;


class branchApiAuth
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
        if ($request->header('Authorization')) {
            if (Auth::guard('branch-api')->check()) {
                try {
                    JWTAuth::parseToken()->authenticate();
                } catch (Exception $exception) {
                    if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                       return response()->json(['message'=>'Token is Invalid']);
                    } else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                        return response()->json(['message'=>'Token is Expired']);
                    } else {
                        return response()->json(['message'=>'Authorization Token not found']);
                    }
                }
                return $next($request);
            }
            return response()->json(['message'=>'please login and return go to request , Invalid Token'],401);
        }
        return response()->json(['message'=>'please login and return go to request , Invalid Token'],401);
    }

}
