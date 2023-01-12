<?php
namespace App\Http\Controllers\User;
use App\Interfaces\OrderRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\response;
use App\Models\Order\order;
use Illuminate\Support\Facades\DB;
use App\Models\Order\OrderDriveryStatus;
use Validator;
use App;
use Auth;
class OrderController extends Controller
{
    //
    use response;
    private OrderRepositoryInterface $OrderRepository;
    public function __construct(OrderRepositoryInterface $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }
    #Reigon[this is show order cycle & make order]
        public function getservices(Request $request){
            $lang=$request->header('lang');
            App::setLocale($lang);
            $services= $this->OrderRepository->getservices($request,$lang);
            $data['services']= $services;
            return $this->response(true,'get services succefully',$data);

        }
        public function selectlaundry(Request $request){
            $lang=$request->header('lang');
            App::setLocale($lang);
            $branchs= $this->OrderRepository->selectlaundry($request,$lang);
            $data['status']=true;
            $data['message']='get branches successfuly';
            $data['data']['branches']=$branchs;
            return response()->json($data);
        }
        public function chooselaundry(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $services= $this->OrderRepository->chooselaundry($request->branch_id,$lang);
        return $this->response(true,'get services succefully',$services);
        }
        public function getcategoryitems(Request $request){
            $lang=$request->header('lang');
            App::setLocale($lang);
            $categoryitems=$this->OrderRepository->getcategoryitems($request->category_id,$request->service_id,$request->branch_id,$lang);
            return $categoryitems;
        }
        public function itemdetailes(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $itemdetailes=$this->OrderRepository->itemdetailes($request->item_id,$lang);
        $data['itemdetailes']=$itemdetailes;
        return $this->response(true,'return item detailes successfulty',$data);
        }
        public function submitorder(Request $request){
         $order=$this->OrderRepository->submitorder($request);
         return $this->response(true,'order creates success',['order_id'=>$order]);
        }
        public function ordersummary(Request $request){
            $ordersummary=$this->OrderRepository->ordersummary($request);
            return $this->response(true,'order summary',$ordersummary);
        }
        public function checkout(Request $request){
            $order_id=$request->order_id;
            $branchid=$request->branch_id;
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
        public function reciveorder(Request $request){
            $data=  $this->OrderRepository->reciveorder($request);
            if ($data==null){
              return $this->response(false,'some thing wrong');
            }
            return $this->response($data['status'],$data['message']);
        }
    #EndReigon[this is show order cycle]
}
