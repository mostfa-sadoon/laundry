<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Laundry\branch;
use App\Models\Laundry\branchcloseingday;
use Illuminate\Http\Request;
use App\Traits\response;
use Validator;
use Auth;
use App;
use Hash;
class ProfileController extends Controller
{
    //
    use response;
    public function edit(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $branchinfo=branch::with('closingdayes')->select('id','username','phone','lat','long','open_time','closed_time')
        ->find($branch_id);
        $data['branchinfo']=$branchinfo;
        return $this->response(true,'return branch ingo successfully',$data);
    }
    public function update(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $branch=branch::find($branch_id);
        $validator =Validator::make($request->all(), [
        'username'=>'required|unique:branchs,username,'.$branch_id,
        'country_code'=>'required',
        'lat'=>'required',
        'long'=>'required',
        'open_time'=>'required',
        'closed_time'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],403);
        }
        DB::transaction(function()use(&$branch,$request)
        {
                $branch->update([
                'username'=>$request->username,
                'country_code'=>$request->country_code,
                'lat'=>$request->lat,
                'long'=>$request->long,
                'open_time'=>$request->open_time,
                'closed_time'=>$request->closed_time
                ]);
                branchcloseingday::where('branch_id',$branch->id)->delete();
                foreach($request->closeing_daies as $closingday){
                branchcloseingday::create([
                    'closeingday_id'=>$closingday,
                    'branch_id'=>$branch->id,
                ]);
                }
        });
        return $this->response(true,'branch info updated successfully');
    }


    public function updatepassword(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $branch=branch::find($branch_id);
        if(!Hash::check($request->old_password, $branch->password)) {
            return $this->response(false, "The specified password does not match the old password");
        }
        $validator =Validator::make($request->all(), [
            'password'=> 'required|min:6|max:50|confirmed',
            'password_confirmation' => 'required|max:50|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['message'=>$validator->messages()->first()],403);
            }
            $branch->update([
                'password' => Hash::make($request->password),
             ]);
        return $this->response(true,'branch password updated successfully');
    }

    public function editphone(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $branch=branch::find($branch_id);
        if($request->phone!=$branch->phone){
            $branch->update([
              'otp'=>1234
            ]);
            return $this->response(true,'please send otp in the next request with phone number');
        }else{
            return $this->response(false,'this number is already exist');
        }
    }
    public function updatephone(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $branch=branch::find($branch_id);
        if($branch->otp==$request->otp && $branch->phone==$request->oldphone){
            $branch->update([
              'phone'=>$request->phone
            ]);
        }else{
            return $this->response(false,'otp is wrong or phone');
        }
        return $this->response(true,'phone updated successfully');
    }
}
