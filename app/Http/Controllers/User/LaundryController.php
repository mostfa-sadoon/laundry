<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laundry\branch;
use App\Models\Laundry\Laundry;
use Validator;
use App\Traits\response;

class LaundryController extends Controller
{
    //
    use response;
    public function laundryinfo(Request $request){
        $validator =Validator::make($request->all(), [
            'branch_id'=>'required',
          ]);
          if ($validator->fails()) {
            return $this->response(false,$validator->messages()->first(),null,401);
          }
           $id=$request->branch_id;
           $branch=branch::select('id','username','laundry_id','lat','long','address')->find($id);
           $avgerge_service_time['ironing']=20;
           $avgerge_service_time['washing']=20;
           $avgerge_service_time['washing&iroing']=20;
           $avgerge_service_time['dry clean']=20;
           $branch->avgerge_service_time=$avgerge_service_time;
           $data['branch_info']=$branch;
           return $this->response(true,'get branch info successfuly',$data);
    }
}
