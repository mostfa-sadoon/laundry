<?php

namespace App\Repositories;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Laundry\branchservice;
use App\Models\laundryservice\Service;
use App\Models\Laundry\Branchitem;
use App\Models\Laundry\BranchitemTranslation;
use App\Models\Laundry\branch;
use App\Http\Resources\editservice\categoryresource;
use App\Http\Resources\editservice\serviceresource;
use App\Models\laundryservice\Additionalservice;
use App\Models\Order\OrderDriveryStatus;
use App\Models\Order\order;
use App\Http\Resources\editservice\branchitem as branchitemresource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order\orderdetailes;
use App\Models\laundryservice\Argent;
use App\Traits\queries\orders;
use App\Traits\queries\serviceTrait;
use Validator;
use Auth;
use App;
class OrderRepository implements OrderRepositoryInterface
{
    use orders,serviceTrait;
    public $service_ids=[];
    public function getservices($request,$lang){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $services=Service::select('id')->get();
        return $services;
    }
    public function selectlaundry($request,$lang){
        $branchs=branch::select('id','username','laundry_id')->whereHas('branchservices',function($q)use($request){
        $q->wherein('service_id',$request->services);
       })->with('laundry',function($q){
        $q->select('name','id')->get();
       })->get();
        return $branchs;
    }
    public function chooselaundry($branch_id,$lang){
            $branch=branch::find($branch_id);
            $data=[];
            $branchservices=branchservice::select('service_id')->where('branch_id',$branch_id)->where('status','on')->get();
            foreach($branchservices as $branchservice){
                array_push($this->service_ids,$branchservice->service_id);
            }
            $services=Service::wherein('id',$this->service_ids)->select('id')->with('categories')->get()->makehidden(['created_at','updated_at']);
            $data['services']= $services;
            $data['branchargent']=$branch->argent;
            return $data;
    }
    public function getcategoryitems($category_id,$service_id,$branch_id,$lang){
      $brnchitem=Branchitem::whereHas('branchitemprice',function($q)use($service_id,$branch_id,$category_id){
            $q->where('service_id',$service_id)->where('branch_id',$branch_id)->where('category_id',$category_id);
        })->with(['branchitemprice'=>function($q)use($service_id,$branch_id,$category_id){
            $q->where('service_id',$service_id)->where('branch_id',$branch_id)->where('category_id',$category_id)->get();
        }])->get();
        return  ['status'=>true,'message'=>'get all pranch item price successfully',
                  'data'=>['branchitem'=> branchitemresource::collection($brnchitem)]
                  ];
      }
    public function itemdetailes($item_id,$lang){
      $itemadditionalservice=Additionalservice::select('id')->whereHas('branchadditionalservice',function(Builder $query)use($item_id){
          $query->where('branchitem_id',$item_id)->where('status','on');
      })->with(['itemprices'=>function($q)use($item_id){
              $q->select('additionalservice_id','price','id as item_price_id')->where('branchitem_id',$item_id)->get();
      }])->get();
      return $itemadditionalservice;
    }
    public function submitorder($request){
        $branchid=$request->branch_id;
        try{
            DB::beginTransaction();
           $user=Auth::user();
           $order=order::create([
               'branch_id'=>$branchid,
               'customer_name'=>$user->name,
               'customer_phone'=>$user->phone,
               'customer_location'=>'test',
               'lat'=>$request->lat,
               'long'=>$request->long,
               'driver_id'=>1,
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
                   'branchitem_id'=>$additionalservice['branchitem_id'],
                   'service_id'=>$additionalservice['service_id'],
                   'price'=>$price,
                   'additionalservice_id'=>$additionalservice['additionalservice_id'],
                   'quantity'=>$additionalservice['quantity']
               ]);
            }
            DB::commit();
            return $order->id;
        }catch(\Exception $ex){
        DB::rollback();
        return false;
        }

    }
    public function reciveorder($request){
        $order_id=$request->order_id;
        $confirm_type=$request->confirm_type;
        $orderstatus=OrderDriveryStatus::where('order_id',$order_id)->latest('id')->first();
        $order=order::find($order_id);
        if($order==null){
            return false;
        }
        $driver_id=$order->driver_id;
        if($confirm_type=='drop_of_laundry'){
            if($orderstatus->order_status=='drop_of_laundry'){
            $orderstatus->update([
                'confirmation'=>true
            ]);
            OrderDriveryStatus::create([
                 'order_id'=>$order_id,
                 'driver_id'=>$driver_id,
                 'order_status'=>'pick_up_laundry'
             ]);
             $order->update([
                'delivery_status'=>'inprogress',
                'progress'=>'inprogress',
             ]);
            }
            $data['status']=true;
            $data['message']='laundry recived order successfully';
        }
        return $data;
    }
    public function unasignedorder($request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $orders=DB::table('orders')
        ->select('orders.id','orders.customer_name')
        ->where('orders.progress','inprogress')
        ->where('checked',true)
        ->where('driver_id',null)
        ->where('branch_id',$branch_id)
        ->get();
        // get service and put it under order
        $orders=$this->orderwithservice($orders,$lang);
        $data['status']=true;
        $data['message']="get inprogress order successfully";
        $data['data']['orders']=$orders;
        return $data;
    }
    // we use this function when recive order to know order info of it
    public function orderinfo($order_id,$lang){
        $order=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
        ->join('orders','orders.id','=','order_detailes.order_id')
        ->selectRaw('orders.id')
        ->selectRaw('sum(order_detailes.price) as price')
        ->groupBy('orders.id')
        ->first();
        if($order==null){
            return response()->json(['status'=>false,'message'=>'this order not found'],401);
        }
        $orderargentprice=DB::table('orders')->where('orders.id',$order_id)
        ->leftjoin('argent','orders.id','=','argent.order_id')
        ->selectRaw('sum(argent.price) as argentprice')
         ->groupBy('argent.order_id')
         ->first();

         $order->price=$order->price+$orderargentprice->argentprice;
        // this query get services with count of item in it
        $services=$this->serive($order_id,null,$lang);
         // this query get items with count of item in it
        $items=$this->items($order_id,null,$lang);
         // this query to get additional service
        $additionals=$this->additionals($order_id,null,$lang);
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
          return $data;
    }
}
