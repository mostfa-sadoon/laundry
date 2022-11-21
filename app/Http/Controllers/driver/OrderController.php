<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver\Driver;
use App\Models\Order\order;
use App\Models\Order\orderdetailes;
use Illuminate\Support\Facades\DB;
use App\Models\Order\OrderDriveryStatus;
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
    public function Acceptorder(Request $request){
       $order_id=$request->order_id;
       $driver_id=Auth::guard('driver_api')->user()->id;
       $order=order::where('driver_id',$driver_id)->where('id',$order_id)->first();
       if($order!=null)
       $order->update([
          'delivery_status'=>'inprogress',
          'progress'=>'indelivery',
       ]);else{
        $data['status']=false;
        $data['message']="this order not found";
        return response()->json($data);
       }
    //    $OrderDriveryStatus=OrderDriveryStatus::where('order_id',$order_id)->first()->update([
    //       'confirmation'=>true
    //    ]);
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
        ->selectRaw('servicetranslations.name as service')
        ->selectRaw('servicetranslations.service_id as service_id')
        ->where('order_detailes.order_id',$order_id)
        ->where('servicetranslations.locale',$lang)
        ->where('branchitemtranslations.locale',$lang)
        ->where('order_detailes.additionalservice_id','=',null)
        ->groupBy('servicetranslations.service_id')
        ->groupBy('servicetranslations.name')
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
            // but additional service in the item
           foreach($items as $key=>$item){
            $item->additonalservice=[];
            foreach($additionals as $additional){
                if($item->item_id==$additional->item_id){
                    array_push($item->additonalservice,$additional);
                }
             }
             //but argent inside item
             foreach($argents as $argent){
                $item->argent=0;
                if($item->item_id==$argent->branchitem_id){
                    $item->argent=$argent->quantity;
                }
             }
           }
            //but argent inside item
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
     public function inprogressorder(Request $request){
        $order_id=$request->order_id;
        $driver_id=Auth::guard('driver_api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        // orders with items
        $orders=DB::table('orders')
        ->select('orders.id','orders.customer_name','orders.customer_phone','orders.customer_location','branchitemtranslations.name')
        ->selectRaw('sum(order_detailes.quantity) as quantity')
        ->where('orders.driver_id',$driver_id)
        ->where('delivery_status','inprogress')
        ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
        ->join('order_detailes','order_detailes.order_id','=','orders.id')
        ->join('branchitemtranslations','branchitemtranslations.branchitem_id','=','order_detailes.branchitem_id')
        ->groupBy('orders.id')->groupBy('orders.customer_name')->groupBy('orders.customer_phone')->groupBy('orders.customer_location')
        ->groupBy('order_detailes.order_id')
        ->groupBy('branchitemtranslations.name')
        ->where('branchitemtranslations.locale',$lang)
        ->get();
         // push item in array
        $items=[];
        foreach($orders as $key=>$order){
            $items[$key]['order_id']=$order->id;
            $items[$key]['name']=$order->name;
            $items[$key]['quantity']=$order->quantity;
        }
        // orders with out item
        $orders=DB::table('orders')
        ->select('orders.id','orders.customer_name','orders.customer_phone','orders.customer_location','order_delivery_status.order_status')
        ->selectRaw('sum(order_detailes.quantity) as quantity')
        ->where('orders.driver_id',$driver_id)
        ->where('delivery_status','inprogress')
        ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
        ->where('order_delivery_status.driver_id',$driver_id)->latest('order_delivery_status.id')
        ->join('order_detailes','order_detailes.order_id','=','orders.id')
        ->groupBy('orders.id')->groupBy('orders.customer_name')->groupBy('orders.customer_phone')->groupBy('orders.customer_location')
        ->groupBy('order_detailes.order_id')
        ->groupBy('order_delivery_status.order_status')
        ->groupBy('order_delivery_status.id')
        ->get();
        foreach($orders as $key=>$order){
            $key=0;
            $order->item=[];
           foreach($items as $item){
             if($item['order_id']==$order->id){
                 $order->item[$key]['quantity']=$item['quantity'];
                 $order->item[$key]['name']=$item['name'];
                 $key++;
             }
           }
        }
        $data['status']=true;
        $data['message']="get in progress orders suceesfully";
        $data['data']['orders']=$orders;
        return response()->json($data);
     }
     public function confirmorder(Request $request){
        $order_id=$request->order_id;
        $confirm_type=$request->confirm_type;
        $driver_id=Auth::guard('driver_api')->user()->id;
        $orderstatus=OrderDriveryStatus::where('order_id',$order_id)->first();
        if($confirm_type=='pick_up_laundy'){
            $orderstatus->update([
                'confirmation'=>true
            ]);
            OrderDriveryStatus::create([
               'order_id'=>$order_id,
                'driver_id'=>$driver_id,
               'order_status'=>'drop_of_home'
            ]);
            $data['status']=true;
            $data['message']='confirm pick up from laundry success';
        }
        if($confirm_type=='drop_of_home'){
            $orderstatus->update([
                'confirmation'=>true
            ]);
            order::find($order_id)->update([
               'progress'=>'completed',
               'delivery_status'=>'completed',
            ]);
            $data['status']=true;
            $data['message']='drop of the order to home successfuly';
        }
        return response()->json($data);
     }
}
