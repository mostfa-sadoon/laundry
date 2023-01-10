<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    public function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
      {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
      }
      public function getdistance(){
        $distance=$this->haversineGreatCircleDistance('30.589180','31.511932','29.587421','31.516489');
        return $distance;
    }
}


