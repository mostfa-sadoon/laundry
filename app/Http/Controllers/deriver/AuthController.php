<?php

namespace App\Http\Controllers\deriver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver\Driver;
use Auth;
use Hash;
class AuthController extends Controller
{
    //
    public function login(Request $request){
       $driver=Driver::where('phone',$request->phone)->first();
       if($driver==null){
          $data['status']=false;
          $data['message']="some thing is wrong";
          return response()->json($data,401);
       }else{
        $driver->update([
          'otp'=>123456,
          'password'=>Hash::make(1234)
        ]);
        $data['status']=false;
        $data['message']="please send otp in the next request";
        return response()->json($data);
       }
    }
    public function sendtoken(Request $request){
       //dd($request->all());
       $password=$request->otp;
       $credentials = request(['otp','phone']);
       $driver=Driver::where('otp',$request->otp)->first();
       if (!$token = auth()->guard('branch-api')->fromUser($driver)) {
        return response()->json(['message' => 'token is false'], 401);
       }
       $data['status']=true;
       $data['message']="login succesfully";
       $data['data']['token']=$token;
       return response()->json($data);
    }
}
