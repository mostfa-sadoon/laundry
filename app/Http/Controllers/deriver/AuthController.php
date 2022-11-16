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
          'otp'=>1234,
          'password'=>Hash::make(1234)
        ]);
        $data['status']=false;
        $data['message']="please send otp in the next request";
        return response()->json($data);
       }
    }
    public function sendtoken(Request $request){
       $driver=Driver::where('otp',$request->otp)->where('phone',$request->phone)->first();
       if($driver==null){
        $data['status']=false;
        $data['message']="some thing is wrong";
        return response()->json($data,401);
       }
       if (!$token = auth()->guard('driver_api')->tokenById($driver->id)) {
        return response()->json(['message' => 'token is false'], 401);
       }
       if($driver->status=='ofline'){
        $driver->update([
            'status'=>'ofline'
        ]);
       }
       $data['status']=true;
       $data['message']="login succesfully";
       $data['data']['token']=$token;
       $data['data']['name']=$driver->name;
       return response()->json($data);
    }
    public function logout(){
        Auth::guard('driver_api')->logout();
        return response()->json([
            'status' => true,
            'message'=>'logout success',
        ]);
    }
}
