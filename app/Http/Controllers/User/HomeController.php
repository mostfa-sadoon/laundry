<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laundry\branch;
use App\Models\Slider;
use App\Traits\response;
use App;
class HomeController extends Controller
{
      //
      use response;
      public function getslider(Request $request){
          $lang=$request->header('lang');
          App::setLocale($lang);
          $sliders=Slider::select('id')->get();
          $data['slider']=$sliders;
          return $this->response(true,'get sliders successfully',$data);
      }
      function sort($arr){
        $n=count($arr);
        if($n<= 1){
            return $arr;
        }
        else{
            $pivot = array();
            $pivot[0]['id'] = $arr[0]['id'];
            $pivot[0]['name'] = $arr[0]['name'];
            $pivot[0]['username'] = $arr[0]['username'];
            $pivot[0]['address'] = $arr[0]['address'];
            $pivot[0]['distance'] = $arr[0]['distance'];
            $left = array();
            $right = array();
            for($i = 1; $i < count($arr); $i++)
            {
                if($arr[$i]['distance'] < $pivot[0]['distance']){
                    $left[] = $arr[$i];
                }
                else{
                    $right[] = $arr[$i];
                }
            }
            return array_merge(self::sort($left), $pivot,self::sort($right));
        }
      }
      function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);

          if ($unit == "K") {
            return ($miles * 1.609344);
          } else if ($unit == "N") {
            return ($miles * 0.8684);
          } else {
            return $miles;
          }
        }
      } // end of distance
      public $toplaundries=[];
      public function toplaundries(Request $request){
        $lat=$request->lat;
        $lon=$request->lon;
        $branches=branch::select('id','username','laundry_id','lat','long','address')->with('laundry',function($q){
            $q->select('name','id')->get();
            })->get()->take(100);
        // but laundries in array
        foreach($branches as $key=>$branch){
                $distance=$this->distance((float)$branch->lat,(float)$branch->long,(float)$lat,(float)$lon,'k');
                $this->toplaundries[$key]['id']=$branch->id;
                $this->toplaundries[$key]['name']=$branch->laundry->name;
                $this->toplaundries[$key]['username']=$branch->username;
                $this->toplaundries[$key]['address']=$branch->address;
                $this->toplaundries[$key]['distance']=$distance;
        }
        // sort array
        $this->toplaundries=$this->sort($this->toplaundries);
        $data['top laundries']=$this->toplaundries;
         return $this->response(true,'top laundries',$data);
        $distance=$this->distance(30.589361,31.511245,30.5855592,31.520983,'k');
        return $distance;
      }
}
