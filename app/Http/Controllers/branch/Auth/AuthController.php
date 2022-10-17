<?php

namespace App\Http\Controllers\branch\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\GeneralTrait;
class AuthController extends Controller
{
    //
    use GeneralTrait;
    public function login(Request $request){
        $credentials = request(['username', 'password']);
        if (!$token = auth()->guard('branch-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
       return $this->returnData('token', $token, $msg = "");
       return $this->respo($token);
    }



    public function test(){
           dd('gfgf');
    }
}
