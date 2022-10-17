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
                       return response()->json(['status' => 'false','message'=>'Token is Invalid']);
                    } else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                        return response()->json(['status' => 'false','message'=>'Token is Expired']);
                    } else {
                        return response()->json(['status' => 'false','message'=>'Authorization Token not found']);
                    }
                }
                return $next($request);
            }
            return response()->json(['status' => 'false','message'=>'please login and return go to request , Invalid Token']);
        }
        return response()->json(['status' => 'false','message'=>'please login and return go to request , Invalid Token']);
    }

}
