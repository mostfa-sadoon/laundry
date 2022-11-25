<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver\Driver;
use App\Models\Order\order;
use App\Traits\response;


class driverController extends Controller
{
    //
    use response;
    public function avilabledriver(Request $request){
      $avilabledriver=Driver::select('id','name')->where('status','online')->get();
      $data['avilabledriver']=$avilabledriver;
     return $this->response(true,'get avilable driver successfully',$data);
    }
    public function assignorder(Request $request){
        $order_id=$request->order_id;
        $driver_id=$request->driver_id;
        order::findorfail($order_id)->update([
            'driver_id'=>$driver_id
        ]);
        return $this->response(true,'assginded '.$order_id.' to  driver id  '.$driver_id.' successfulty');
    }
    public function assignorders(Request $request){
        $driver_id=$request->driver_id;
        foreach($request->orders as $order){
            order::findorfail($order)->update([
                'driver_id'=>$driver_id
            ]);
        }
        return $this->response(true,'assign order successfully');
    }
}
