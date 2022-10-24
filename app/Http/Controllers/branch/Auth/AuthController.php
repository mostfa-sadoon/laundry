<?php

namespace App\Http\Controllers\branch\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\GeneralTrait;
use App\Models\Laundry\branch;
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
            return response()->json(['error' => 'Unauthorized'], 401);
        }
       return $this->returnData('token', $token, $msg = "",200);
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
            $logo=null;
            if($request->logo){
            $logo=$this->MoveImage($request->file('logo'),'uploads/branches/logos');
            }
                $branch=branch::create([
                    'email'=>$request->email,
                    'username'=>$request->username,
                    'country_code'=>$request->country_code,
                    'phone'=>$request->phone,
                    'status'=>'open',
                    'lat'=>$request->lat,
                    'long'=>$request->long,
                    'laundry_id'=>$request->laundry_id,
                    'logo'=>$logo,
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
