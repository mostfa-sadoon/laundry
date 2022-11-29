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
use App\Models\Driver\Driver;
use Illuminate\Support\Facades\DB;
use App\Models\Order\order;
use App\Models\Order\orderdetailes;
use App\Models\laundryservice\Serviceitemprice;
use App\Models\Order\OrderDriveryStatus;
use App\Traits\queries\serviceTrait;
use App\Models\Laundry\branch;
use Auth;
use App;

class OrderController extends Controller
{
    use serviceTrait;
    #Reigon[this is create ordder cycle]
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
            'driver_id'=>1,
         ]);
         OrderDriveryStatus::create([
            'order_id'=>$order->id,
            'driver_id'=>1,
            'order_status'=>'pick_up_laundy'
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
        //  $orderdetailes= DB::select('select service.name
        //  from(select
        //   order_detailes.service_id ,servicetranslations.name ,price, quantity
        //   from order_detailes
        //   INNER   join servicetranslations
        //   ON
        //   order_detailes.service_id =servicetranslations.service_id
        //   where order_detailes.order_id = :id
        //   And
        //   servicetranslations.locale=:lang) as service
        //   group by service_id
        //   ',['id' => $order_id,
        //   'lang'=>$lang
        //   ]);
            $orderdetailes= DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
          ->join('servicetranslations','servicetranslations.service_id','=','order_detailes.service_id')
          ->selectRaw('sum(price) as total')
          ->selectRaw('sum(quantity) as quantity')
          ->selectRaw('servicetranslations.name')
          ->where('order_detailes.order_id',$order_id)
          ->where('servicetranslations.locale',$lang)
          ->where('order_detailes.additionalservice_id','=',null)
          ->groupBy('servicetranslations.service_id')
          ->groupBy('servicetranslations.name')
          ->get();
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
    #EndReigon
    #Reigon[this is show order cycle]
        public function inprogressorder(Request $request){
            $branch_id=Auth::guard('branch-api')->user()->id;
            $lang=$request->header('lang');
            App::setLocale($lang);
            $orders=DB::table('orders')
            ->select('orders.id','orders.customer_name')
            ->where('orders.progress','inprogress')
            ->where('branch_id',$branch_id)
            ->get();
            // get service and put it under order
            foreach($orders as $order){
              array_push($this->orders_id,$order->id);
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
             // get additoional service to put it under service
            $additionalservices=DB::table('order_detailes')
            ->select('order_id','order_detailes.service_id','order_detailes.additionalservice_id')
            ->selectRaw('sum(quantity) as quantity')
            ->wherein('order_detailes.order_id',$this->orders_id)
            ->where('order_detailes.additionalservice_id','!=',null)
            ->groupBy('order_detailes.order_id')
            ->groupBy('order_detailes.service_id')
            ->groupBy('order_detailes.additionalservice_id')
            ->get();
             foreach($services as $service){
                $service->additionalservice='';
                foreach($additionalservices as $additionalservice){
                    if($service->service_id == $additionalservice->service_id){
                        $service->additionalservice=$additionalservice->quantity;
                    }
                }
             }
            foreach($orders as $order){
                $order->services=[];
                foreach($services as $service){
                  if($service->order_id==$order->id){
                    array_push($order->services,$service);
                  }
                }
            }
            $data['status']=true;
            $data['message']="get inprogress order successfully";
            $data['data']['orders']=$orders;
            return response()->json($data);
        }
        public $orders_id=[];
        public function completedorder(Request $request){
            $branch_id=Auth::guard('branch-api')->user()->id;
            $lang=$request->header('lang');
            App::setLocale($lang);
            $orders=DB::table('orders')
            ->select('orders.id','orders.customer_name')
            ->where('orders.progress','completed')
            ->where('branch_id',$branch_id)
            ->paginate(20);
            // get service and put it under order
            foreach($orders as $order){
              array_push($this->orders_id,$order->id);
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
             // get additoional service to put it under service
            $additionalservices=DB::table('order_detailes')
            ->select('order_id','order_detailes.service_id','order_detailes.additionalservice_id')
            ->selectRaw('sum(quantity) as quantity')
            ->wherein('order_detailes.order_id',$this->orders_id)
            ->where('order_detailes.additionalservice_id','!=',null)
            ->groupBy('order_detailes.order_id')
            ->groupBy('order_detailes.service_id')
            ->groupBy('order_detailes.additionalservice_id')
            ->get();
             foreach($services as $service){
                $service->additionalservice='';
                foreach($additionalservices as $additionalservice){
                    if($service->service_id == $additionalservice->service_id){
                        $service->additionalservice=$additionalservice->quantity;
                    }
                }
             }
            foreach($orders as $order){
                $order->services=[];
                foreach($services as $service){
                  if($service->order_id==$order->id){
                    array_push($order->services,$service);
                  }
                }
            }
            $data['status']=true;
            $data['message']='get completed order successfully';
            $data['data']['orders']=$orders;
            return response()->json($orders);
        }
        public $drivers_id=[];
        public function indeliveryorder(Request $request){
            $lang=$request->header('lang');
            App::setLocale($lang);
            $branch_id=Auth::guard('branch-api')->user()->id;
            // get driver that have order

            // $drivers=DB::table('drivers')
            // ->select('drivers.id as driver_id','drivers.name','orders.id as order_id')
            // ->where('branch_id',$branch_id)
            // ->join('orders','drivers.id','=','orders.driver_id')
            // ->where('delivery_status','inprogress')
            // ->wherein('orders.id',function($q){
            //     $q->from('orders')->groupBy('orders.driver_id')->selectRaw('MAX(orders.id)')
            //     ->distinct()->get();
            // })->get();

            $drivers=DB::table('drivers')
            ->select('drivers.id as driver_id','drivers.name')->where('branch_id',$branch_id)
            ->join('orders','drivers.id','=','orders.driver_id')
            ->where('delivery_status','inprogress')
            ->distinct()
            ->get();
            // get orders of drivers
            foreach($drivers as $driver){
                array_push($this->drivers_id,$driver->driver_id);
            }
            $orders=[];
            foreach($drivers as $driver){
                $order=DB::table('orders')->where('driver_id',$driver->driver_id)
                ->select('orders.id as order_id','driver_id')->where('delivery_status','inprogress')
                ->latest('orders.id')
                ->first();
                array_push($orders,$order);
            }
            $orderscount=Order::select('driver_id','id as order_id')->where('branch_id',$branch_id)->where('delivery_status','inprogress')
            ->groupBy('orders.driver_id')
            ->groupBy('orders.id')
            ->get();
            //get orders id
            foreach($orders as $order){
            array_push($this->orders_id,$order->order_id);
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
            foreach($orders as $order){
                $order->services=[];
                foreach ($services as $service){
                    if($order->order_id==$service->order_id){
                        array_push($order->services,$service);
                    }
                }
            }
            foreach($drivers as $driver){
                $driver->ordercount=0;
                foreach($orderscount as $count){
                    if($count->driver_id==$driver->driver_id){
                      $driver->ordercount+=1;
                    }
                  }
            }

      //     return response()->json($orders);
            foreach($drivers as $driver){
                $driver->order=[];
                foreach($orders as $order){
                    if($order->driver_id==$driver->driver_id){
                       //$driver->order=$order;
                       foreach ($services as $service){
                        if($order->order_id==$service->order_id){
                            array_push($driver->order,$service);
                        }
                    }
                    }
                }
            }
            $data['status']=true;
            $data['message']='get completed order successfully';
            $data['data']['drivers']=$drivers;
            return response()->json($data);
   }

   public function moreorder(Request $request){
      $driver_id=$request->driver_id;
      $branch_id=Auth::guard('branch-api')->user()->id;
      $lang=$request->header('lang');
      App::setLocale($lang);
      $driver=Driver::select('id','name')->find($driver_id);
      $orders=DB::table('orders')
      ->select('orders.id as order_id','driver_id')
      ->where('branch_id',$branch_id)
      ->where('drivers.id',$driver_id)
      ->join('drivers','drivers.id','=','orders.driver_id')
      ->where('delivery_status','inprogress')
      ->latest('orders.id')->distinct()
      ->get();
      // get orders of drivers
      $orderscount=Order::select('driver_id','id as order_id')
      ->where('driver_id',$driver_id)
      ->where('branch_id',$branch_id)->where('delivery_status','inprogress')
      ->groupBy('orders.driver_id')
      ->groupBy('orders.id')
      ->get();
      //get orders id
      foreach($orders as $order){
      array_push($this->orders_id,$order->order_id);
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
          foreach($additionalservices as $additionalservice){
              if($service->order_id == $additionalservice->order_id && $service->service_id == $additionalservice->service_id){
                  array_push($service->additionalservice,$additionalservice);
              }
          }
       }
       // but services inside drivers
       $driver->orderscount=0;
       foreach($orderscount as $ordercount){
        if($driver->id==$ordercount->driver_id)
        {
            $driver->orderscount+=1;
        }
       }
       foreach($orders as $order){
          $order->services=[];
              foreach($services as $service){
                  if($service->order_id  == $order->order_id){
                      array_push($order->services,$service);
                  }
              }
        }
      $data['status']=true;
      $data['message']='get completed order successfully';
      $data['data']['driver']=$driver;
      $data['data']['orders']=$orders;
      return response()->json($data);
   }
   public function ordersummary(Request $request){
    $order_id=$request->order_id;
    $driver_id=DB::table('orders')->select('driver_id')->where('orders.id',$order_id)->first()->driver_id;
    $driver=Driver::select('name')->find($driver_id);
    $lang=$request->header('lang');
    App::setLocale($lang);
    $order=DB::table('orders')
    ->select('orders.delivery_status','orders.customer_location','orders.id as order_id','orders.customer_name')
    ->join('order_detailes','order_detailes.order_id','=','orders.id')
    ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
    ->where('order_delivery_status.driver_id',$driver_id)->latest('order_delivery_status.id')
  //  ->where('order_delivery_status.confirmation',false)
    ->where('orders.id',$order_id)
    ->where('order_detailes.order_id',$order_id)
    ->selectRaw('orders.created_at')
    ->selectRaw('order_delivery_status.order_status')
    ->selectRaw('sum(order_detailes.price) as price')
    ->groupBy('orders.id')
    ->groupBy('orders.customer_location')
    ->groupBy('orders.created_at')
    ->groupBy('orders.delivery_status')
    ->groupBy('orders.customer_name')
    ->groupBy('order_delivery_status.order_id')
    ->groupBy('order_delivery_status.id')
    ->groupBy('order_delivery_status.order_status')
    ->orderBy('order_delivery_status.created_at','desc')
    ->first();
  //  dd($order);
    $order->created_at=date('Y-m-d', strtotime($order->created_at));
    $order->time=date('h:m a', strtotime($order->created_at));
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
      $data['data']['driver']=$driver;
      $data['data']['order']=$order;
      $data['data']['serives']=$services;
      return response()->json($data);

   }
   #EndReigon
}
