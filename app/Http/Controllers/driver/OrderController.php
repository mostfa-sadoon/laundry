<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver\Driver;
use App\Models\Order\order;
use App\Models\Order\orderdetailes;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use App;
class OrderController extends Controller
{
    //
    public function getneworder(){
        $driver_id=Auth::guard('driver_api')->user()->id;
        $orders=order::select('id','customer_name','customer_phone','customer_location')->where('driver_id',$driver_id)->where('delivery_status','=',null)->get();
        $data['status']=true;
        $data['message']="get new orders suceesfully";
        $data['data']['orders']=$orders;
        return response()->json($data);
    }
    public function confirmorder(Request $request){
       $order_id=$request->order_id;
       $driver_id=Auth::guard('driver_api')->user()->id;
       $order=order::where('driver_id',$driver_id)->where('id',$order_id)->first();
       $order->update([
          'delivery_status'=>'inprogress',
          'progress'=>'indelivery',
       ]);
       $data['status']=true;
       $data['message']="confirm order suceesfully";
       return response()->json($data);
    }
    public function rejectorder(Request $request){
        $order_id=$request->order_id;
        $driver_id=Auth::guard('driver_api')->user()->id;
        $order=order::where('driver_id',$driver_id)->where('id',$order_id)->first();
        $order->update([
           'driver_id'=>null
        ]);
        $data['status']=true;
        $data['message']="reject order suceesfully";
        return response()->json($data);
    }
    //
    public function orderinfo(Request $request){
        $order_id=$request->order_id;
        $driver_id=Auth::guard('driver_api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $order=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->selectRaw('orders.id')
        ->selectRaw('sum(order_detailes.price) as price')
        ->groupBy('orders.id')
        ->first();
        $orderargentprice=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->join('argent','orders.id','=','argent.order_id')
        ->selectRaw('sum(argent.price) as argentprice')
         ->groupBy('argent.id')
         ->first();
         $order->price=$order->price+$orderargentprice->argentprice;
        // this query get services with count of item in it
        $services=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->join('servicetranslations','servicetranslations.service_id','=','order_detailes.service_id')
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->join('branchitems','branchitems.id','=','order_detailes.branchitem_id')
        ->join('branchitemtranslations','branchitemtranslations.branchitem_id','=','branchitems.id')
        ->selectRaw('sum(quantity) as quantity')
        //->selectRaw('order_detailes.quantity as quantityitem')
        ->selectRaw('servicetranslations.name as service')
        ->selectRaw('servicetranslations.service_id as service_id')
        //->selectRaw('branchitemtranslations.name')
        ->where('order_detailes.order_id',$order_id)
        ->where('servicetranslations.locale',$lang)
        ->where('branchitemtranslations.locale',$lang)
        ->where('order_detailes.additionalservice_id','=',null)
        ->groupBy('servicetranslations.service_id')
        ->groupBy('servicetranslations.name')
        //->groupBy('branchitemtranslations.name')
        ->where('orders.driver_id',$driver_id)->where('order_detailes.order_id',$order_id)
        ->get();
         // this query get items with count of item in it
        $items=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->join('servicetranslations','servicetranslations.service_id','=','order_detailes.service_id')
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->join('branchitems','branchitems.id','=','order_detailes.branchitem_id')
        ->join('branchitemtranslations','branchitemtranslations.branchitem_id','=','branchitems.id')
        ->selectRaw('branchitemtranslations.name')
        ->selectRaw('sum(quantity) as quantity')
        ->selectRaw('branchitemtranslations.branchitem_id as item_id')
        ->selectRaw('servicetranslations.service_id')
        ->where('order_detailes.order_id',$order_id)
        ->where('servicetranslations.locale',$lang)
        ->where('branchitemtranslations.locale',$lang)
        ->where('order_detailes.additionalservice_id','=',null)
        ->groupBy('servicetranslations.service_id')
        ->groupBy('servicetranslations.name')
        ->groupBy('branchitemtranslations.name')
        ->groupBy('branchitemtranslations.branchitem_id')
        ->where('orders.driver_id',$driver_id)->where('order_detailes.order_id',$order_id)
        ->get();
         // this query to get additional service
        $additionals=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->join('additionalservicetranslations','additionalservicetranslations.additionalservice_id','=','order_detailes.additionalservice_id')
        ->join('branchitemtranslations','branchitemtranslations.branchitem_id','=','order_detailes.branchitem_id')
        ->selectRaw('order_detailes.service_id')
        ->selectRaw('sum(quantity) as quantity')
         ->selectRaw('branchitemtranslations.name as item')
         ->selectRaw('branchitemtranslations.branchitem_id as item_id')
         ->selectRaw('additionalservicetranslations.name')
        ->where('orders.driver_id',$driver_id)->where('order_detailes.order_id',$order_id)
        ->where('order_detailes.additionalservice_id','!=',null)
        ->groupBy('additionalservicetranslations.additionalservice_id')
        ->groupBy('additionalservicetranslations.name')
        ->groupBy('branchitemtranslations.name')
        ->groupBy('branchitemtranslations.branchitem_id')
        ->groupBy('order_detailes.service_id')
       ->where('additionalservicetranslations.locale',$lang)
       ->where('branchitemtranslations.locale',$lang)
        ->get();

       $argents=db::table('argent')->where('order_id',$order_id)->get();



           foreach($items as $key=>$item){
            $item->additonalservice=[];
            foreach($additionals as $additional){
                if($item->item_id==$additional->item_id){
                    array_push($item->additonalservice,$additional);
                }
             }
             foreach($argents as $argent){
                $item->argent=0;
                if($item->item_id==$argent->branchitem_id){
                    $item->argent=$argent->quantity;
                }
             }
           }

          foreach($services as $key=>$service){
            $service->item=[];
             foreach($items as $item){
                if($item->service_id==$service->service_id){
                    array_push($service->item,$item);
                }
             }
          }






        $data['status']=true;
        $data['message']="get new orders suceesfully";
        $data['data']['order']=$order;
        $data['data']['serives']=$services;

        return response()->json($data);
    }
}
