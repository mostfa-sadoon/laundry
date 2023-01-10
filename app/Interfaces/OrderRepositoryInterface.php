<?php

namespace App\Interfaces;

interface OrderRepositoryInterface
{
  // we use it when customer make order
  public function getservices($request,$lang);
  // we use it to filter laundries when user enter services he need
  public function selectlaundry($request,$lang);
  public function chooselaundry($laundryid,$lang);
  public function getcategoryitems($category_id,$service_id,$branch_id,$lang);
  public function itemdetailes($itemid,$lang);
  public function submitorder($request);
  public function reciveorder($request);
  public function unasignedorder($request);
  public function orderinfo($order_id,$lang);
  public function checkout($request);
}
