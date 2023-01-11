<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Laundry\branch;
use App\Models\Laundry\Laundry;
use App\Models\Laundry\branchservice;
use Validator;
use App\Traits\response;
use App;
class LaundryController extends Controller
{
    //
    use response;
    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
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
    }
     // end of distance
    public function laundryinfo(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $validator =Validator::make($request->all(), [
            'branch_id'=>'required',
            'lat'=>'required',
            'long'=>'required',
          ]);
          if ($validator->fails()) {
            return $this->response(false,$validator->messages()->first(),null,401);
          }
           $id=$request->branch_id;
            $branch=branch::select('id','username','laundry_id','lat','long','address')->with('laundry',function($q){
            $q->select('id','name','logo')->get();
            })->find($id);
            $branch->distance=$this->distance((float)$branch->lat,(float)$branch->long,(float)$request->lat,(float)$request->long,'k');
            $brnach_services=DB::table('brnachservices')
            ->select('servicetranslations.name')
            ->join('servicetranslations','servicetranslations.service_id','=','brnachservices.service_id')
            ->where('servicetranslations.locale',$lang)
            ->where('brnachservices.branch_id',$id)->get();
            foreach($brnach_services as $service){
                $service=$service->time=20;
            }
           $branch->brnach_services=$brnach_services;
           $branch->laundry->rate='very good';
           $branch->delivery_fees=500;
           $branch->averge_service_time=22;
           $branch->live_tracking=true;
           $data['branch_info']=$branch;
           return $this->response(true,'get branch info successfuly',$data);
    }
}
