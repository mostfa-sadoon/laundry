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
    public function laundryinfo(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $validator =Validator::make($request->all(), [
            'branch_id'=>'required',
          ]);
          if ($validator->fails()) {
            return $this->response(false,$validator->messages()->first(),null,401);
          }
           $id=$request->branch_id;
            $branch=branch::select('id','username','laundry_id','lat','long','address')->with('laundry',function($q){
            $q->select('name','id')->get();
            })->find($id);
            $brnach_services=DB::table('brnachservices')
            ->select('servicetranslations.name')
            ->join('servicetranslations','servicetranslations.service_id','=','brnachservices.service_id')
            ->where('servicetranslations.locale',$lang)
            ->where('brnachservices.branch_id',$id)->get();
            foreach($brnach_services as $service){
                $service=$service->time=20;
            }
           $branch->brnach_services=$brnach_services;
           $data['branch_info']=$branch;
           return $this->response(true,'get branch info successfuly',$data);
    }
}
