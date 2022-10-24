<?php

namespace App\Http\Controllers\Laundry\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\GeneralTrait;
use App\Models\Laundry\Laundry;
use App\Traits\fileTrait;
use Validator;
use Hash;

class AuthController extends Controller
{
    //
    use fileTrait;
    use GeneralTrait;
    public function login(Request $request){
        $credentials = request(['email', 'password']);
        if (!$token = auth()->guard('laundry-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
       return $this->returnData('token', $token, $msg = "");
       return $this->respo($token);
    }

    public function registration(Request $request){
      // dd($request->all());
      $validator =Validator::make($request->all(), [
         'name'=>'required|unique:laundry_translations',
         'email'=>'required|unique:laundries',
         'country_code'=>'required',
         'password'=> 'required|min:6|max:50|confirmed',
         'password_confirmation' => 'required|max:50|min:6',
         'branch_number'=>'required',
         'company_register'=>'required',
         'tax_card'=>'required',
       ]);
       if ($validator->fails()) {
        return response()->json([
            'status'=>false,
            'message'=>$validator->messages()->first()
        ]);
        }
       $company_register=$this->MoveImage($request->file('company_register'),'uploads/laundry/company_register');
       $tax_card=$this->MoveImage($request->file('tax_card'),'uploads/laundry/tax_card');
       $logo=null;
       if($request->logo){
        $logo=$this->MoveImage($request->file('logo'),'uploads/laundry/logos');
       }
      $laundry= Laundry::create([
        'phone'=>$request->phone,
        'country_code'=>$request->country_code,
        'email'=>$request->email,
        'status'=>'disactive',
        'branch'=>$request->branch_number,
        'companyregister'=> $company_register,
        'taxcard'=>$tax_card,
        'logo'=>$logo,
        'password' => Hash::make($request->password),
       ]);
       return $this->returnData('laundry_id', $laundry->id, $msg = "laundery added succesffuly",200);
    }


    public function test(){
        dd('gfgf');
    }
}
