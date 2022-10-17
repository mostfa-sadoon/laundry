<?php

namespace App\Http\Controllers\Laundry\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\GeneralTrait;

class AuthController extends Controller
{
    //
    use GeneralTrait;
    public function login(Request $request){
        $credentials = request(['email', 'password']);
        if (!$token = auth()->guard('laundry-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
       return $this->returnData('token', $token, $msg = "");
       return $this->respo($token);
    }


    public function test(){
        dd('gfgf');
 }
}
