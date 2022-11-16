<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver\Driver;
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
        $driver=Driver::select('name','email','phone')->find($driver_id);
        $data['status']=true;
        $data['message']="get driver info";
        $data['data']['driver']=$driver;
        return response()->json($data);
    }
    public function updateinfo(Request $request){
        dd($request->all);
        $driver=Driver::select('name','email','phone')->find($driver_id);
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
              $data['message']="please send otp in the next request";
              return response()->json($data);
        }
    }
}
