<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver\Driver;
use Validator;

use Auth;

class driverController extends Controller
{
    //
    public function updatestatus(Request $request){
    $driver_id=Auth::guard('driver_api')->user()->id;
        $driver=Driver::find($driver_id);
        if($driver->status=='online'){
           $driver->update(['status'=>'ofline']);
        }else{
            $driver->update(['status'=>'online']);
        }
        $data['status']=true;
        $data['message']="status updated to ".$driver->status;
        $data['data']['driver_status']=$driver->status;
        return response()->json($data);
    }
    public function driverinfo()
    {
        $driver_id=Auth::guard('driver_api')->user()->id;
        $driver=Driver::select('name','email','phone','id')->find($driver_id);
        $data['status']=true;
        $data['message']="get driver info";
        $data['data']['driver']=$driver;
        return response()->json($data);
    }
    public function updateinfo(Request $request){
        $driver_id=Auth::guard('driver_api')->user()->id;
        $driver=Driver::select('name','email','phone')->find($driver_id);
        $validator =Validator::make($request->all(),[
            'name'=>'required|unique:drivers',
            'email'=>'required|unique:drivers,email,'.$driver_id,
            'phone'=>'required|unique:drivers,phone,'.$driver_id,
          ]);
          if ($validator->fails()) {
           return response()->json([
               'message'=>$validator->messages()->first()
           ],403);
           }

        if($request->phone==$driver->phone){
            $driver->update(request(['name','email']));
            $data['status']=true;
            $data['message']="profile info updated successfully";
            return response()->json($data);
        }else{
            $driver->update([
                'otp'=>1234,
              ]);
              $data['status']=true;
              $data['message']="please send otp in the next request with phone number";
              return response()->json($data);
        }
    }

    public function updatephone(Request $request){
        $driver=Driver::where('otp',$request->otp)->first();
        if($driver==null){
         $data['status']=false;
         $data['message']="some thing is wrong";
         return response()->json($data,401);
        }
        if (!$token = auth()->guard('driver_api')->tokenById($driver->id)) {
         return response()->json(['message' => 'token is false'], 401);
        }
         $driver->update([
             'otp'=>null
         ]);
        $driver->update([
           'phone'=>$request->phone
        ]);
        $data['status']=true;
        $data['message']="profile info updated successfully";
        return response()->json($data);
    }
}
