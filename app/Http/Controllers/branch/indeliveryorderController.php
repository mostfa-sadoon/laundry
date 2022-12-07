<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Order\order;
use App\Traits\response;
use App\Traits\queries\serviceTrait;
use App\Traits\queries\orders;
use Auth;
use App;

class indeliveryorderController extends Controller
{
    //
    use response,serviceTrait,orders;
    public $orders_id=[];
    public function driverorder(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $drivers=DB::table('drivers')
        ->join('orders','drivers.id','orders.driver_id')
        ->select('orders.driver_id','drivers.name','orders.id as order_id')
        ->where('delivery_status','inprogress')
        ->where('orders.branch_id',$branch_id)
        ->get();
        foreach($drivers as $driver){
            $driver->ordercount=0;
            foreach($drivers as $iterationdriver){
               if($driver->driver_id==$iterationdriver->driver_id){
                 $driver->ordercount+=1;
               }
            }
        }
        foreach($drivers as $driver){
            array_push($this->orders_id,$driver->order_id);
        }
        $services=DB::table('order_detailes')
        ->select('servicetranslations.name','order_id','order_detailes.service_id')
        ->join('servicetranslations','servicetranslations.service_id','=','order_detailes.service_id')
        ->selectRaw('sum(quantity) as quantity')
        ->wherein('order_detailes.order_id',$this->orders_id)
        ->where('order_detailes.additionalservice_id','=',null)
        ->where('servicetranslations.locale',$lang)
        ->groupBy('order_detailes.order_id')
        ->groupBy('order_detailes.service_id')
        ->groupBy('servicetranslations.service_id')
        ->groupBy('servicetranslations.name')
        ->get();
        $additionalservices=DB::table('order_detailes')
        ->select('order_id','order_detailes.service_id','order_detailes.additionalservice_id','additionalservicetranslations.name')
        ->join('additionalservicetranslations','additionalservicetranslations.additionalservice_id','=','order_detailes.additionalservice_id')
        ->selectRaw('sum(quantity) as quantity')
        ->wherein('order_detailes.order_id',$this->orders_id)
        ->where('order_detailes.additionalservice_id','!=',null)
        ->where('additionalservicetranslations.locale',$lang)
        ->groupBy('additionalservicetranslations.name')
        ->groupBy('order_detailes.order_id')
        ->groupBy('order_detailes.service_id')
        ->groupBy('order_detailes.additionalservice_id')
        ->get();
        // but additional service inside service
        foreach($services as $service){
            $service->additionalservice=[];
            foreach($additionalservices as $key=>$additionalservice){
                if($service->order_id == $additionalservice->order_id && $service->service_id == $additionalservice->service_id){
                    array_push($service->additionalservice,$additionalservice);
                }
            }
         }
         foreach($drivers as $driver){
            $driver->order=[];
             foreach($services as $service){
                if($service->order_id==$driver->order_id){
                     array_push($driver->order,$service);
                }
             }
             unset($driver->order_id);
         }
        $data['drivers']=$drivers;
        return $this->response(true,'get dirvers with orders success',$data);
    }
    public function customerorder(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $orders=DB::table('orders')
        ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
        ->select('orders.id','orders.customer_name')
        ->where('order_delivery_status.order_status','drop_of_laundry')
        ->where('orders.delivery_type_id',3)->get();
        $orders=$this->orderwithservice($orders,$lang);
        $data['orders']=$orders;
        return $this->response(true,'get balance success',$data);
    }
}
