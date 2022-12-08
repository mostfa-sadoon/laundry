<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use App\Models\Laundry\branch;
use Illuminate\Http\Request;
use App\Traits\response;
use Carbon\Carbon;
use Auth;

class SettingController extends Controller
{
    //
    use response;
    public function updatestatus(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $branch=branch::find($branch_id);
        if($branch->status=='open'){
            $branch->update([
                'status'=>'closed'
              ]);
        }else{
            $branch->update([
                'status'=>'open'
              ]);
        }
        return $this->response(true,'update pranch status successfully');
    }   
    public function avilabledayes(){
        $date=[];
         for($i=0;$i<7;$i++){
            array_push($date, date('d-m-y', strtotime("+$i days")));
          }
        $data['date']=$date;
        return $this->response(true,'avilable dates',$data);
    }
}
