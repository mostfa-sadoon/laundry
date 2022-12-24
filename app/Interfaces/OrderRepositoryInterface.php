<?php

namespace App\Interfaces;

interface OrderRepositoryInterface
{
  public function selectlaundry($laundryid,$lang);
  public function getcategoryitems($category_id,$service_id,$branch_id,$lang);
  public function itemdetailes($itemid,$lang);
  public function submitorder($request);
  public function reciveorder($request);
  public function unasignedorder($request);
}
