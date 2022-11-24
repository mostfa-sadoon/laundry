<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver\Driver;
use App\Models\Order\order;
use App\Models\Order\orderdetailes;
use Illuminate\Support\Facades\DB;
use App\Models\Order\OrderDriveryStatus;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Traits\queries\serviceTrait;
use Validator;
use Auth;
use App;
class OrderController extends Controller
{
    //
    use serviceTrait;
    public function getneworder(){
        $driver_id=Auth::guard('driver_api')->user()->id;
        $orders=DB::table('orders')->where('orders.driver_id',$driver_id)->where('orders.delivery_status','=',null)
        ->select('orders.id','customer_name','customer_phone','customer_location','order_delivery_status.order_status','lat','long')
        ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
        ->where('order_delivery_status.driver_id',$driver_id)->latest('order_delivery_status.id')
        ->groupBy('orders.id')->groupBy('orders.customer_name')->groupBy('orders.customer_phone')->groupBy('orders.customer_location')
        ->groupBy('orders.lat')->groupBy('orders.long')
        ->groupBy('order_delivery_status.order_status')
        ->groupBy('order_delivery_status.id')
        ->get();
        foreach($orders as $order){
            $order->distance=5;
        }
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
        $services=$this->serive($order_id,$driver_id,$lang);
         // this query get items with count of item in it
        $items=$this->items($order_id,$driver_id,$lang);
         // this query to get additional service
        $additionals=$this->additionals($order_id,$driver_id,$lang);
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
        ->select('orders.id','orders.customer_name','orders.customer_phone','orders.customer_location','order_delivery_status.order_status','lat','long')
        ->selectRaw('sum(order_detailes.quantity) as quantity')
        ->selectRaw('sum(argent.price +order_detailes.price) as price')
        ->where('orders.driver_id',$driver_id)
        ->where('delivery_status','inprogress')
        ->join('argent','orders.id','=','argent.order_id')
        ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
        ->where('order_delivery_status.driver_id',$driver_id)->latest('order_delivery_status.id')
        ->where('order_delivery_status.confirmation',false)
        ->join('order_detailes','order_detailes.order_id','=','orders.id')
        ->groupBy('orders.id')->groupBy('orders.customer_name')->groupBy('orders.customer_phone')->groupBy('orders.customer_location')
        ->groupBy('orders.lat') ->groupBy('orders.long')
        ->groupBy('order_detailes.order_id')
        ->groupBy('order_delivery_status.order_status')
        ->groupBy('order_delivery_status.id')
        ->groupBy('argent.order_id')
        ->get();
        foreach($orders as $key=>$order){
            $order->distance=5;
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
     public function latestorder(Request $request){
        $driver_id=Auth::guard('driver_api')->user()->id;
        $latestorders=DB::table('orders')
        ->select('orders.id','orders.customer_name','orders.customer_phone','orders.customer_location','delivery_status','orders.created_at')
        ->selectRaw('sum(order_detailes.quantity) as quantity')
        ->where('orders.driver_id',$driver_id)
        ->where('orders.delivery_status','!=',null)
        ->join('order_detailes','order_detailes.order_id','=','orders.id')
        ->groupBy('orders.id')->groupBy('orders.customer_name')->groupBy('orders.customer_phone')->groupBy('orders.customer_location')
        ->groupBy('orders.delivery_status')->groupBy('orders.created_at')
        ->groupBy('order_detailes.order_id')
        ->get();
        foreach($latestorders as $latestorder){
            $latestorder->created_at=date('Y-m-d', strtotime($latestorder->created_at));
            $latestorder->time=date('h:m a', strtotime($latestorder->created_at));
        }
        $data['status']=true;
        $data['message']="get new orders suceesfully";
        $data['data']['orders']=$latestorders;
       // $data['data']['completedorder']=$completedorder;
        return response()->json($data);
     }
     public function latestorderinfo(Request $request){
           $order_id=$request->order_id;
        $driver_id=Auth::guard('driver_api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $order=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->select('orders.delivery_status','orders.created_at','orders.customer_location')
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
        ->where('order_delivery_status.driver_id',$driver_id)->latest('order_delivery_status.id')
        ->where('order_delivery_status.confirmation',false)
        ->selectRaw('orders.id')
        ->selectRaw('order_delivery_status.order_status')
        ->selectRaw('sum(order_detailes.price) as price')
        ->groupBy('orders.id')
        ->groupBy('orders.customer_location')
        ->groupBy('orders.created_at')
        ->groupBy('orders.delivery_status')
        ->groupBy('order_delivery_status.order_id')
        ->groupBy('order_delivery_status.id')
        ->groupBy('order_delivery_status.order_status')
        ->first();
       // $order->created_at=date('Y-m-d', strtotime($order->created_at));
      //  $order->time=date('h:m a', strtotime($order->created_at));
        $orderargentprice=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->join('argent','orders.id','=','argent.order_id')
        ->selectRaw('sum(argent.price) as argentprice')
         ->groupBy('argent.id')
         ->first();
         $order->price=$order->price+$orderargentprice->argentprice;
        // this query get services with count of item in it
        $services=$this->serive($order_id,$driver_id,$lang);
         // this query get items with count of item in it
         $items=$this->items($order_id,$driver_id,$lang);
         // this query to get additional service
         $additionals=$this->additionals($order_id,$driver_id,$lang);
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
     public function allorder(Request $request){
        $driver_id=Auth::guard('driver_api')->user()->id;
        $allorders=DB::table('orders')
        ->select('orders.id','orders.customer_name','orders.customer_phone','orders.customer_location','delivery_status','orders.created_at')
        ->selectRaw('sum(order_detailes.quantity) as quantity')
        ->where('orders.driver_id',$driver_id)
        ->where('orders.delivery_status','!=',null)
        ->join('order_detailes','order_detailes.order_id','=','orders.id')
        ->groupBy('orders.id')->groupBy('orders.customer_name')->groupBy('orders.customer_phone')->groupBy('orders.customer_location')
        ->groupBy('orders.delivery_status')->groupBy('orders.created_at')
        ->groupBy('order_detailes.order_id')
        ->paginate(3);
        foreach($allorders as $allorder){
            $allorder->created_at=date('Y-m-d', strtotime($allorder->created_at));
            $allorder->time=date('h:m a', strtotime($allorder->created_at));
        }
        $data['status']=true;
        $data['message']="get new orders suceesfully";
        $data['data']=$allorders;
        $allorders->status=true;
        $allorders->message='fvdv';
        return response()->json($allorders);
     }
}
