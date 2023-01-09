<?php
namespace App\Http\Controllers\User;
use App\Interfaces\OrderRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\response;
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
    #Reigon[this is show order cycle]
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

}
