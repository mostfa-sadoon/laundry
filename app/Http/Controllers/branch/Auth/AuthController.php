<?php

namespace App\Http\Controllers\branch\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\GeneralTrait;
use App\Models\Laundry\branch;
use Validator;
use Hash;

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

    public function registration(Request $request){
        $validator =Validator::make($request->all(), [
            'username'=>'required|unique:laundry_translations',
            'email'=>'required|unique:laundries',
            'country_code'=>'required',
            'password'=> 'required|min:6|max:50|confirmed',
            'password_confirmation' => 'required|max:50|min:6',
            'laundry_id'=>'required|exists:App\Models\Laundry\laundry,id',
          ]);
          if ($validator->fails()) {
           return response()->json([
               'status'=>false,
               'message'=>$validator->messages()->first()
           ]);
           }
           $branch=branch::create([
                'email'=>$request->email,
                'username'=>$request->username,
                'country_code'=>$request->country_code,
                'phone'=>$request->phone,
                'status'=>'open',
                'lat'=>'2023453',
                'long'=>'2556230',
                'laundry_id'=>1,
                'logo'=>'vfvdvfd.jpg',
                'password' => Hash::make(123456),
           ]);
    }


    public function test(){
           dd('gfgf');
    }
}
