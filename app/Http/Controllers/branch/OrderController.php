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
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order\delivery_type;
use App\Models\Order\payment_method;
use Illuminate\Support\Facades\DB;
use App\Models\Order\order;
use App\Models\Order\orderdetailes;
use App\Models\laundryservice\Serviceitemprice;

use Auth;
use App;

class OrderController extends Controller
{
    //
    public $service_ids=[];
    public function getservice(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $data=[];
        $branchservices=branchservice::select('service_id')->where('branch_id',$branch_id)->get();
        foreach($branchservices as $branchservice){
            array_push($this->service_ids,$branchservice->service_id);
        }
        $services=Service::wherein('id',$this->service_ids)->select('id')->with('categories')->get()->makehidden(['created_at','updated_at']);
        $data['services']= $services;
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
    public function orderinfo(Request $request){
        $lang=$request->header('lang');
        $order_id=$request->order_id;
        App::setLocale($lang);
        $deliverytype=delivery_type::select('id')->get()->makehidden('translations');
        $paymentmethods=payment_method::select('id')->get()->makehidden('translations');
        // $order=Order::with(['orderdetailes'=>function($q){
        //     $q->where('service_id','!=',null)->get();
        // }])->find($order_id);
         $orderdetailes=orderdetailes::select('service_id','quantity','price')
         ->with(['service'=>function($q){
            $q->select('id')->get();
         }])
         ->where('order_id',$order_id)
         ->get()
         ->groupBy('service_id');

         $orderdetailes= DB::select('select sum(price) , service_id
         from order_detailes where order_id = :id
         group By service_id
         inner join servicetranslations
         on
          servicetranslations.service_id =order_detailes.service_id
         and
         servicetranslations.locale=:lang

         ', ['id' => $order_id,
             'lang'=>$lang
         ]);

        // $orderdetailes = DB::table('order_detailes')
        // ->join('services','services.id','=','order_detailes.service_id')
        // ->select('price','service_id')
        // ->where('service_id','!=',null)
        // ->get()
        // ->groupBy('service_id')
        //  ->sum(DB::raw('price'))
        ;



        //   $orderdetailes=orderdetailes::select('price')->get()->groupby('service_id');

        $data['status']=true;
        $data['message']="return order info succeffuly";
        $data['data']['deliverytype']=$deliverytype;
        $data['data']['paymentmethods']=$paymentmethods;
        $data['data']['orderdetailes']=$orderdetailes;
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
            foreach($serviceprice['additionalservice'] as $additionalservice){
                $brnchitemprice=Serviceitemprice::select('price')->where('branchitem_id',$serviceprice['branchitem_id'])->where('additionalservice_id',$additionalservice)->first();

                $price=$brnchitemprice->price*$serviceprice['quantity'];

                orderdetailes::create([
                    'order_id'=>$order->id,
                    'branchitem_id'=>$serviceprice['branchitem_id'],
                    'service_id'=>$serviceprice['service_id'],
                    'price'=>$price,
                    'additionalservice_id'=>$additionalservice,
                    'quantity'=>$serviceprice['quantity']
                  ]);
            }
        }
    });
} catch (Throwable $e) {
    report($e);
    return false;
}
    $data['status']=true;
    $data['message']='order added succefully';
    $data['dara']['order']=$order->id;
     return response()->json($data);
    }
}
