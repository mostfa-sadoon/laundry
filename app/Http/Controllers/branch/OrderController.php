<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laundry\branchservice;
use App\Models\laundryservice\Service;
use App\Models\laundryservice\branchAdditionalservice;
use App\Models\Laundry\Branchitem;
use App\Models\Laundry\BranchitemTranslation;
use App\Models\laundryservice\Additionalservice;
use App\Models\laundryservice\Argent;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order\delivery_type;
use App\Models\Order\payment_method;
use Illuminate\Support\Facades\DB;
use App\Models\Order\order;
use App\Models\Order\orderdetailes;
use App\Models\laundryservice\Serviceitemprice;
use App\Models\Laundry\branch;
use Auth;
use App;

class OrderController extends Controller
{
    //
    public $service_ids=[];
    public function getservice(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $branch=branch::find($branch_id);
        $lang=$request->header('lang');
        App::setLocale($lang);
        $data=[];
        $branchservices=branchservice::select('service_id')->where('branch_id',$branch_id)->where('status','on')->get();
        foreach($branchservices as $branchservice){
            array_push($this->service_ids,$branchservice->service_id);
        }
        $services=Service::wherein('id',$this->service_ids)->select('id')->with('categories')->get()->makehidden(['created_at','updated_at']);
        $data['services']= $services;
        $data['branchargent']=$branch->argent;
        return response()->json(['status'=>true,'message'=>'get services succefully','data'=>$data]);
    }
    public function itemdetailes(Request $request){
     $lang=$request->header('lang');
     App::setLocale($lang);
     $item_id=$request->item_id;
     $itemadditionalservice=Additionalservice::select('id')->whereHas('branchadditionalservice',function(Builder $query)use($item_id){
        $query->where('branchitem_id',$item_id)->where('status','on');
     })->with(['itemprices'=>function($q)use($item_id){
            $q->select('additionalservice_id','price','id as item_price_id')->where('branchitem_id',$item_id)->get();
     }])->get();
     $data['status']=true;
     $data['message']="return avavilable additional service of this item";
     $data['data']['additionalservices']=$itemadditionalservice;
     return response()->json($data);
    }
    public function submitorder(Request $request){
       // dd($request->all());
        try{
         $branchid=Auth::guard('branch-api')->user()->id;
         DB::transaction(function()use(&$order,$request,$branchid)
         {
         $order=order::create([
            'branch_id'=>$branchid,
            'customer_name'=>$request->customer_name,
            'customer_phone'=>$request->customer_phone,
            'customer_location'=>$request->customer_location,
            'lat'=>$request->lat,
            'long'=>$request->long,
         ]);
         foreach($request->serviceprices as  $serviceprice){
             $price=$serviceprice['quantity']*$serviceprice['price'];
             orderdetailes::create([
                 'order_id'=>$order->id,
                 'branchitem_id'=>$serviceprice['branchitem_id'],
                 'price'=> $price,
                 'service_id'=>$serviceprice['service_id'],
                 'quantity'=>$serviceprice['quantity']
               ]);
               // this condation to add agent
               if($serviceprice['argent']!=0){
                $argentprice=Branchitem::select('argentprice')->where('id',$serviceprice['branchitem_id'])->first();
                $argentprice=$serviceprice['argent']*$argentprice->argentprice;
                Argent::create([
                    'order_id'=>$order->id,
                    'price'=>$argentprice,
                    'quantity'=>$serviceprice['argent'],
                    'service_id'=>$serviceprice['service_id'],
                    'branchitem_id'=>$serviceprice['branchitem_id'],
                ]);
             }
         }
         foreach($request->additionalservices as $additionalservice){
             orderdetailes::create([
                 'order_id'=>$order->id,
                 'branchitem_id'=>$serviceprice['branchitem_id'],
                 'service_id'=>$serviceprice['service_id'],
                 'price'=>$price,
                 'additionalservice_id'=>$additionalservice['additionalservice_id'],
                 'quantity'=>$serviceprice['quantity']
               ]);
         }

     });
    } catch (Throwable $e) {
        report($e);
        return false;
    }
        $data['status']=true;
        $data['message']='order added succefully';
        $data['data']['order']=$order->id;
        return response()->json($data);
    }
    public function cancelorder(Request $request){
      $order_id=$request->order_id;
      $branchid=Auth::guard('branch-api')->user()->id;
      $order=order::where('id',$order_id)->where('branch_id',$branchid)->first();
      if($order==null){
        $data['status']=false;
        $data['message']='order not found';
        return response()->json($data,405);
      }else{
        $order->delete();
        $data['status']=true;
        $data['message']='order cancel succefully';
        return response()->json($data);
      }
    }
    public function orderinfo(Request $request){
        $lang=$request->header('lang');
        $order_id=$request->order_id;
        App::setLocale($lang);
        $deliverytype=delivery_type::select('id')->get()->makehidden('translations');
        $paymentmethods=payment_method::select('id')->get()->makehidden('translations');
         $orderdetailes= DB::select('select service.name, sum(price) as price ,sum(service.quantity) as quantity
         from(select
          order_detailes.service_id ,servicetranslations.name ,price, quantity
          from order_detailes
          INNER   join servicetranslations
          ON
          order_detailes.service_id =servicetranslations.service_id
          where order_detailes.order_id = :id
          And
          servicetranslations.locale=:lang) as service
          group by service_id
           ',['id' => $order_id,
           'lang'=>$lang
          ]);
        $argentprice=Argent::where('order_id',$order_id)->sum('price');
        $data['status']=true;
        $data['message']="return order info succeffuly";
        $data['data']['deliverytype']=$deliverytype;
        $data['data']['paymentmethods']=$paymentmethods;
        $data['data']['orderdetailes']=$orderdetailes;
        $data['data']['argentprice']= $argentprice;
        return response()->json($data);
    }
    public function checkorder(Request $request){
        $order_id=$request->order_id;
        $branchid=Auth::guard('branch-api')->user()->id;
        $order=order::where('id',$order_id)->where('branch_id',$branchid)->first();
        if($order==null){
            $data['status']=false;
            $data['message']='order not found';
            return response()->json($data,405);
          }else{
            $order->update([
               'checked'=>true
            ]);
            $data['status']=true;
            $data['message']='order checled  succefully';
            return response()->json($data);
          }
    }
}
