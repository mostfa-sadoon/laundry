<?php
namespace App\Traits\queries;
use Illuminate\Support\Facades\DB;
trait orders
{
  public $orders_id=[];
  public function orderwithservice($orders,$lang){
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
      ->select('order_id','order_detailes.service_id','order_detailes.additionalservice_id','additionalservicetranslations.name as name')
      ->join('additionalservicetranslations','additionalservicetranslations.additionalservice_id','=','additionalservicetranslations.additionalservice_id')
      ->selectRaw('sum(quantity) as quantity')
      ->wherein('order_detailes.order_id',$this->orders_id)
      ->where('order_detailes.additionalservice_id','!=',null)
      ->where('additionalservicetranslations.locale',$lang)
      ->groupBy('additionalservicetranslations.name')
      ->groupBy('order_detailes.order_id')
      ->groupBy('order_detailes.service_id')
      ->groupBy('order_detailes.additionalservice_id')
      ->get();
       foreach($services as $service){
          $service->additionalservice=[];
          foreach($additionalservices as $additionalservice){
              if($service->service_id == $additionalservice->service_id &&  $additionalservice->order_id==$service->order_id){
                 array_push($service->additionalservice,$additionalservice);
              }
          }
       }
       $argents=db::table('argent')->wherein('order_id',$this->orders_id)->get();
       foreach($services as $service){
        foreach($argents as $argent){
            $service->argent=0;
            if($service->service_id==$argent->service_id&&$service->order_id == $argent->order_id){
                $service->argent=$argent->quantity;
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
      return $orders;
  }
}
