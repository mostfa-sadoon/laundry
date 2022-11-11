<?php

namespace App\Http\Controllers\branch\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\GeneralTrait;
use App\Models\Laundry\branch;
use App\Models\Laundry\Laundry;
use App\Models\Laundry\branchcloseingday;
use App\Traits\fileTrait;
use Illuminate\Support\Facades\DB;
use Validator;
use Hash;

class AuthController extends Controller
{
    //
    use GeneralTrait;
    use fileTrait;
    public function login(Request $request){
        $credentials = request(['username', 'password']);
        if (!$token = auth()->guard('branch-api')->attempt($credentials)) {
            return response()->json(['message' => 'Your Branch username or password maybe incorrect, please try agian'], 401);
        }
        $branchid=Auth::guard('branch-api')->user()->id;
        $branch=branch::find($branchid);
        $laundry=Laundry::find($branch->laundry_id);
       // dd($branch);
        if($laundry->status=='false'){
            $data['message']='your laundry activation is false';

            return response()->json($data,401);
        }
        $data['status']=true;
        $data['message']="login succesfully";
        $data['data']['token']=$token;
        return response()->json($data);
       // return response()->json(['message'=>'login success','branch'=>$branch,'token'=>$token]);
       return $this->returnData('data', $data, $msg = "login success",200);
       return $this->respo($token);
    }
    public function logout(){
        Auth::guard('branch-api')->logout();
        return response()->json([
            'status' => true,
            'message'=>'logout success',
        ]);
    }
    public function registration(Request $request){
        $laundry_id=Auth::guard('laundry_api')->user()->id;
        $validator =Validator::make($request->all(), [
            'username'=>'required|unique:branchs',
            'country_code'=>'required',
            'phone'=>'required|unique:branchs',
            'password'=> 'required|min:6|max:50|confirmed',
            'password_confirmation' => 'required|max:50|min:6',
            'address'=>'required',
            'lat'=>'required',
            'long'=>'required',
            'open_time'=>'required',
            'closed_time'=>'required',
           // 'laundry_id'=>'required|exists:App\Models\Laundry\Laundry,id',
          ]);
          if ($validator->fails()) {
           return response()->json([
               'message'=>$validator->messages()->first()
           ],403);
           }

           DB::transaction(function()use(&$branch,$laundry_id,$request)
           {
                $branch=branch::create([
                    'username'=>$request->username,
                    'country_code'=>$request->country_code,
                    'phone'=>$request->phone,
                    'status'=>'open',
                    'lat'=>$request->lat,
                    'long'=>$request->long,
                    'open_time'=>$request->open_time,
                    'closed_time'=>$request->closed_time,
                    'address'=>$request->address,
                    'laundry_id'=>$laundry_id,
                    'password' => Hash::make($request->password),
                  ]);
                  foreach($request->closeing_daies as $closeingday){
                    branchcloseingday::create([
                        'closeingday_id'=>$closeingday,
                        'branch_id'=>$branch->id,
                    ]);
                  }
           });
            //    $credentials = ['username'=>$branch->username,
            //                   'password'=>$request->password];
            //    if (!$token = auth()->guard('branch-api')->attempt($credentials)) {
            //        return response()->json(['message' => 'Unauthorized'], 401);
            //    }
            $data=[];
            $data['status']=true;
            $data['message']="branch added successfully";
           // $data['data']['laundry_activation']='false';
            $data['data']['branch_id']=$branch->id;
            return response()->json($data,200);
    }


    public function test(){
           dd('gfgf');
    }
}
