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
            $q->select('additionalservice_id','price')->where('branchitem_id',$item_id)->get();
     }])->get();
     $data['status']=true;
     $data['message']="return avavilable additional service of this item";
     $data['data']['additionalservices']=$itemadditionalservice;
     return response()->json($data);
    }
    public function orderinfo(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $deliverytype=delivery_type::select('id')->get()->makehidden('translations');
        $paymentmethods=payment_method::select('id')->get()->makehidden('translations');
        $data['status']=true;
        $data['message']="return order info succeffuly";
        $data['data']['deliverytype']=$deliverytype;
        $data['data']['paymentmethods']=$paymentmethods;
        return response()->json($data);
    }
}
