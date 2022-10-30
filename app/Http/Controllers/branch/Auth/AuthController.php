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
            return response()->json(['error' => 'Your Branch username or password maybe incorrect, please try agian'], 401);
        }
        $branchid=Auth::guard('branch-api')->user()->id;
        $branch=branch::find($branchid);
        $laundry=Laundry::find($branch->laundry_id);
       // dd($branch);
        if($laundry->status=='false'){
            $data['message']='your laundry activation is false';

            return response()->json($data,401);
        }
        $data['token']=$token;
        return response()->json($data);
       // return response()->json(['message'=>'login success','branch'=>$branch,'token'=>$token]);
       return $this->returnData('data', $data, $msg = "login success",200);
       return $this->respo($token);
    }
    public function registration(Request $request){
        //dd($request->all());
        $validator =Validator::make($request->all(), [
            'username'=>'required|unique:branchs',
            'email'=>'required|unique:laundries',
            'country_code'=>'required',
            'phone'=>'required|unique:branchs',
            'password'=> 'required|min:6|max:50|confirmed',
            'password_confirmation' => 'required|max:50|min:6',
            'laundry_id'=>'required|exists:App\Models\Laundry\laundry,id',
          ]);
          if ($validator->fails()) {
           return response()->json([
               'status'=>false,
               'message'=>$validator->messages()->first()
           ]);
           }

           DB::transaction(function()use(&$branch,$request)
           {
                $branch=branch::create([
                    'email'=>$request->email,
                    'username'=>$request->username,
                    'country_code'=>$request->country_code,
                    'phone'=>$request->phone,
                    'status'=>'open',
                    'lat'=>$request->lat,
                    'long'=>$request->long,

                    'open_time'=>$request->open,
                    'closed_time'=>$request->closed,

                    'laundry_id'=>$request->laundry_id,
                    'password' => Hash::make($request->password),
                  ]);
                  foreach($request->closeing_daies as $closeingday){
                    branchcloseingday::create([
                        'closeingday_id'=>$closeingday,
                        'branch_id'=>$branch->id,
                    ]);
                  }
           });
           $credentials = ['username'=>$branch->username,
                          'password'=>$request->password];
           if (!$token = auth()->guard('branch-api')->attempt($credentials)) {
               return response()->json(['error' => 'Unauthorized'], 401);
           }
            $data=[];
            $data['branch_id']=$branch->id;
            $data['token']=$token;
           return $this->returnData('branch', $data, $msg = "branch added succesffuly",200);
    }


    public function test(){
           dd('gfgf');
    }
}
