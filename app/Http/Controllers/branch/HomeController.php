<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Order\order;
use App\Traits\response;
use Auth;
use App;
use Carbon\Carbon;

class HomeController extends Controller
{
    //
    use response;
    public function getpalance(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $Balance=DB::table('orders')
        ->join('order_detailes','order_detailes.order_id','=','orders.id')
        ->selectRaw('sum(order_detailes.price) as price')
        ->where('branch_id',$branch_id)
        ->where('checked',true)
        ->groupBy('orders.branch_id')
        ->first();
        $argentpalance=DB::table('orders')
        ->join('argent','argent.order_id','=','orders.id')
        ->selectRaw('sum(argent.price) as price')
        ->where('orders.branch_id',$branch_id)
        ->where('checked',true)
        ->groupBy('orders.branch_id')
        ->first();
        $Balancetoday=DB::table('orders')
        ->join('order_detailes','order_detailes.order_id','=','orders.id')
        ->selectRaw('sum(order_detailes.price) as price')
        ->where('branch_id',$branch_id)
        ->where('checked',true)
        ->whereDate('orders.created_at', Carbon::today())
        ->groupBy('orders.branch_id')
        ->groupBy('orders.created_at')
        ->first();
        $argentpalancetoday=DB::table('orders')
        ->join('argent','argent.order_id','=','orders.id')
        ->selectRaw('sum(argent.price) as price')
        ->where('orders.branch_id',$branch_id)
        ->where('checked',true)
        ->groupBy('orders.created_at')
        ->groupBy('orders.branch_id')
        ->whereDate('orders.created_at', Carbon::today())
        ->first();
        $Balance->price+=$argentpalance->price;
        if($Balancetoday==null){
            $data['Balancetoday']=0;
        }else{
            $Balancetoday->price+=$argentpalancetoday->price;
            $data['Balancetoday']=$Balancetoday;
        }
        $data['Balance']=$Balance;
        return $this->response(true,'get balance success',$data);
    }
    public function branchinfo(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $branch=DB::table('branchs')
        ->join('laundries','laundries.id','=','branchs.laundry_id')
        ->select('branchs.status as status','laundries.name as name','laundries.logo as logo')
        ->where('branchs.id',$branch_id)
        ->first();
        $data['branch']=$branch;
        return $this->response(true,'get branch info successfully',$data);
    }
}
