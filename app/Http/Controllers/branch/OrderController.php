<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laundry\branchservice;
use App\Models\laundryservice\Service;

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
       $item_id=$request->item_id;
       
    }
}
