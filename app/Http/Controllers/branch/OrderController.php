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
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\BranchRepositoryInterface;
use App\Models\Order\OrderDriveryStatus;
use App\Traits\queries\serviceTrait;
use App\Traits\response;
use App\Traits\queries\orders;
use App\Models\Laundry\branch;
use Validator;
use Auth;
use App;

class OrderController extends Controller
{
    use serviceTrait,orders,response;
    use response;
    private OrderRepositoryInterface $OrderRepository;
    public function __construct(OrderRepositoryInterface $OrderRepository,BranchRepositoryInterface $BranchRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }
    #Reigon[this is create ordder cycle]
        public $service_ids=[];
        public function getservice(Request $request){
            $branch_id=Auth::guard('branch-api')->user()->id;
            $lang=$request->header('lang');
            App::setLocale($lang);
            //$services= $this->OrderRepository->selectlaundry($branch_id,$lang);
            $services=Service::select('id')->with('categories')->get();
            $data['services']=$services;
            return $this->response(true,'get services succefully',$data);
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
        public function getopentime(Request $request){
            $branch_id=Auth::guard('branch-api')->user()->id;
            $opentime=$this->BranchRepository->getopentime($branch_id);
            if($opentime==false)
            return $this->response(false,'some thing is wrong',$data=null,401);
            return $this->response(true,'opening hours get successfuly',$opentime);
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
              if($order->checked==true){
                return $this->response(false,'this order alerdy checked',$data=null,403);
              }
            // if order found
            DB::beginTransaction();
              // delvivery consisit of threemain type(self delivery - one way delivery - by delivery)
              if($request->delivery_type=='bydelivery'){
                 $delivery_type_id=2;
                 $order_status='pick_up_home';
              }
              elseif($request->delivery_type=='on_way_delivery')
              {
                     //start validate way of delivery
                       $delivery_type_id=3;
                       $validator =Validator::make($request->all(), [
                         'way_delivery'=>'required',
                        ]);
                        if($validator->fails()){
                            return response()->json([
                                'message'=>$validator->messages()->first()
                            ],403);
                        }

                       //  end validate way of delivery
                        if($request->way_delivery=='home_drop_of'){
                            $order_status='pick_up_laundry';
                        }
                        elseif($request->way_delivery=='self_drop_of'){
                            $order_status='pick_up_home';
                        }
                       else{
                            return response()->json(['message'=>'the way delivery input is false'],403);
                        }
                }
                elseif($request->delivery_type=='self_delivery'){
                        $delivery_type_id=1;
                        $order_status=null;
                }
                    OrderDriveryStatus::create([
                        'order_id'=>$order->id,
                        'driver_id'=>1,
                        'order_status'=>$order_status
                    ]);
                    $order->update([
                        'day'=>$request->day,
                        'from'=>$request->from,
                        'to'=>$request->to,
                        'delivery_type_id'=>$delivery_type_id,
                        'checked'=>true
                    ]);
            DB::commit();
            $data['status']=true;
            $data['message']='order checled  succefully';
            return response()->json($data);
          }
        }
    #EndReigon
     ###################################################################################################################
     ###################################################################################################################
     ###################################################################################################################
     ###################################################################################################################
    #Reigon[this is show order cycle]
        public function inprogressorder(Request $request){
            $branch_id=Auth::guard('branch-api')->user()->id;
            $lang=$request->header('lang');
            App::setLocale($lang);
            $orders=DB::table('orders')
            ->select('orders.id','orders.customer_name')
            ->where('orders.progress','inprogress')
            ->where('checked',true)
            ->where('branch_id',$branch_id)
            ->get();
            // get service and put it under order
            $orders=$this->orderwithservice($orders,$lang);
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
            ->where('checked',true)
            ->where('branch_id',$branch_id)
            ->paginate(3);
            // get service and put it under order
            $orders=$this->orderwithservice($orders,$lang);
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
                $order=DB::table('orders')->where('orders.driver_id',$driver->driver_id)
                ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
                ->select('orders.id as order_id','orders.driver_id','order_delivery_status.order_status as order_status')
                ->where('orders.delivery_status','inprogress')
                ->where('checked',true)
                ->where('order_delivery_status.confirmation',false)
                ->latest('orders.id')
                ->first();
                array_push($orders,$order);
            }
            //return response()->json($orders);
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
            $argents=db::table('argent')->wherein('order_id',$this->orders_id)->get();
            foreach($services as $service){
                $service->additionalservice=[];
                foreach($additionalservices as $key=>$additionalservice){
                    if($service->order_id == $additionalservice->order_id && $service->service_id == $additionalservice->service_id){
                        array_push($service->additionalservice,$additionalservice);
                    }
                }
                foreach($argents as $argent){
                    $service->argent=0;
                    if($service->service_id==$argent->service_id&&$service->order_id == $argent->order_id){
                        $service->argent=$argent->quantity;
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
                $driver->distance=5;
                foreach($orderscount as $count){
                    if($count->driver_id==$driver->driver_id){
                      $driver->ordercount+=1;
                    }
                  }
            }
            foreach($drivers as $driver){
                $driver->orderstatus='';
                $driver->order=[];
                foreach($orders as $order){
                    if($order->driver_id==$driver->driver_id){
                       $driver->orderstatus=$order->order_status;
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
            ->select('orders.id as order_id','orders.driver_id','order_delivery_status.order_status as order_status')
            ->join('order_delivery_status','order_delivery_status.order_id','=','orders.id')
            ->where('branch_id',$branch_id)
            ->where('drivers.id',$driver_id)
            ->where('order_delivery_status.confirmation',false)
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
            $argents=db::table('argent')->wherein('order_id',$this->orders_id)->get();
                foreach($services as $service){
                    foreach($argents as $argent){
                        $service->argent=0;
                        if($service->service_id==$argent->service_id&&$service->order_id == $argent->order_id){
                            $service->argent=$argent->quantity;
                        }
                    }
                }
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
            $order->created_at=date('Y-m-d', strtotime($order->created_at));
            $order->time=date('h:m a', strtotime($order->created_at));
            $orderargentprice=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
            ->join('orders','orders.id','=','order_detailes.order_id')
            ->leftjoin('argent','orders.id','=','argent.order_id')
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
                    array_push($item->additonalservice,$additional->name);
                }
                }
                //but argent inside item
                foreach($argents as $argent){
                $item->argent=false;
                if($item->item_id==$argent->branchitem_id){
                    $item->argent=true;
                    //$item->argent=$argent->quantity;
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
        public function serachorder(Request $request){
                    //dd($request->all());
                    $branch_id=Auth::guard('branch-api')->user()->id;
                    $lang=$request->header('lang');
                    App::setLocale($lang);
                    $type=gettype($request->key);
                    $search_type=$request->search_type;
                    ($type=='integer')? $serach_key='id' : $serach_key='customer_name';
                    $orders=DB::table('orders')
                    ->select('orders.id','orders.customer_name')
                    ->where('orders.progress',$search_type)
                    ->where('branch_id',$branch_id)
                    ->where($serach_key,$request->key)
                    ->latest()
                    ->get();
                    $orders=$this->orderwithservice($orders,$lang);
                    if($orders->isEmpty()){
                        return $this->response(false,'this order not found');
                    }
                    $data['orders']=$orders;
                    return $this->response(true,'return orders success',$data);
        }
        public function unasignedorder(Request $request){
            $orders=$this->OrderRepository->unasignedorder($request);
            return $orders;
        }
        public function reciveorderinfo(Request $request){
            $order_id=$request->order_id;
            $lang=$request->header('lang');
            $orders=$this->OrderRepository->orderinfo($order_id,$lang);
            return $orders;
        }
    #EndReigon
    #Reigon[this is confirm order cycle]
        public function reciveorder(Request $request){
          $data=  $this->OrderRepository->reciveorder($request);
          if ($data==null){
            return $this->response(false,'some thing wrong');
          }
          return $this->response($data['status'],$data['message']);
        }
    #endReigon
}
