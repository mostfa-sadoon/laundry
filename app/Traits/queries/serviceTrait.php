<?php
namespace App\Traits\queries;
use Illuminate\Support\Facades\DB;
trait serviceTrait
{

function serive($order_id,$driver_id,$lang){
   $services= DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
    ->join('servicetranslations','servicetranslations.service_id','=','order_detailes.service_id')
    ->join('orders','orders.id','=','order_detailes.order_id')
    ->join('branchitems','branchitems.id','=','order_detailes.branchitem_id')
    ->join('branchitemtranslations','branchitemtranslations.branchitem_id','=','branchitems.id')
    ->selectRaw('sum(quantity) as quantity')
    ->selectRaw('servicetranslations.name as service')
    ->selectRaw('servicetranslations.service_id as service_id')
    ->where('order_detailes.order_id',$order_id)
    ->where('servicetranslations.locale',$lang)
    ->where('branchitemtranslations.locale',$lang)
    ->where('order_detailes.additionalservice_id','=',null)
    ->groupBy('servicetranslations.service_id')
    ->groupBy('servicetranslations.name')
    ->where('orders.driver_id',$driver_id)->where('order_detailes.order_id',$order_id)
    ->get();
    return $services;
}
public function items($order_id,$driver_id,$lang){
    $items= DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
    ->join('servicetranslations','servicetranslations.service_id','=','order_detailes.service_id')
    ->join('orders','orders.id','=','order_detailes.order_id')
    ->join('branchitems','branchitems.id','=','order_detailes.branchitem_id')
    ->join('branchitemtranslations','branchitemtranslations.branchitem_id','=','branchitems.id')
    ->selectRaw('branchitemtranslations.name')
    ->selectRaw('sum(quantity) as quantity')
    ->selectRaw('branchitemtranslations.branchitem_id as item_id')
    ->selectRaw('servicetranslations.service_id')
    ->where('order_detailes.order_id',$order_id)
    ->where('servicetranslations.locale',$lang)
    ->where('branchitemtranslations.locale',$lang)
    ->where('order_detailes.additionalservice_id','=',null)
    ->groupBy('servicetranslations.service_id')
    ->groupBy('servicetranslations.name')
    ->groupBy('branchitemtranslations.name')
    ->groupBy('branchitemtranslations.branchitem_id')
    ->where('orders.driver_id',$driver_id)->where('order_detailes.order_id',$order_id)
    ->get();
    return $items;
}
public function additionals($order_id,$driver_id,$lang){
    $additionals=DB::table('order_detailes')->where('order_detailes.order_id',$order_id)
    ->join('orders','orders.id','=','order_detailes.order_id')
    ->join('additionalservicetranslations','additionalservicetranslations.additionalservice_id','=','order_detailes.additionalservice_id')
    ->join('branchitemtranslations','branchitemtranslations.branchitem_id','=','order_detailes.branchitem_id')
    ->selectRaw('order_detailes.service_id')
    ->selectRaw('sum(quantity) as quantity')
    ->selectRaw('branchitemtranslations.name as item')
    ->selectRaw('branchitemtranslations.branchitem_id as item_id')
    ->selectRaw('additionalservicetranslations.name')
    ->where('orders.driver_id',$driver_id)->where('order_detailes.order_id',$order_id)
    ->where('order_detailes.additionalservice_id','!=',null)
    ->groupBy('additionalservicetranslations.additionalservice_id')
    ->groupBy('additionalservicetranslations.name')
    ->groupBy('branchitemtranslations.name')
    ->groupBy('branchitemtranslations.branchitem_id')
    ->groupBy('order_detailes.service_id')
    ->where('additionalservicetranslations.locale',$lang)
    ->where('branchitemtranslations.locale',$lang)
    ->get();
    return $additionals;
}

}
