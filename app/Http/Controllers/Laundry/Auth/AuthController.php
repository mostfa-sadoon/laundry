<?php

namespace App\Http\Controllers\Laundry\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Traits\GeneralTrait;
use App\Models\Laundry\branch;
use App\Models\Laundry\Laundry;
use App\Traits\fileTrait;
use App\Http\Resources\Branchinfo;
use Validator;
use Hash;
use App;

class AuthController extends Controller
{
    //
    use fileTrait;
    use GeneralTrait;
    public function login(Request $request){
        $credentials = request(['email', 'password']);
        if (!$token = auth('laundry_api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
      // return response()->json(Auth::guard('laundry-api')->check());

        return response()->json($token);
        // return $this->returnData('token', $token, $msg = "");
        // return $this->respo($token);
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
         'phone'=>'required|unique:laundries',
         'tax_card'=>'required',
       ]);
       if ($validator->fails()) {
        return response()->json([
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
        'branch'=>$request->branch_number,
        'companyregister'=> $company_register,
        'taxcard'=>$tax_card,
        'logo'=>$logo,
        'password' => Hash::make($request->password),
       ]);

      //  dd($laundry);
       $credentials = ['email'=>$laundry->email,
       'password'=>$request->password];
        if (!$token = auth()->guard('laundry_api')->attempt($credentials)) {
        return response()->json(['error' => 'Your Branch username or password maybe incorrect, please try agian'], 401);
        }
        $data=[];
        $data['message']='laundery added succesffuly';
        $data['laundry_id']=$laundry->id;
        $data['token']=$token;
        return response()->json($data);
        //return $this->returnData($data,200);
    }


    public function getpranchinfo(Request $request){
       $lang=$request->header('lang');
       App::setLocale($lang);
       $laundry_id=Auth::guard('laundry_api')->user()->id;
       $branches=branch::select('address','id','open_time','closed_time','closed_time')->where('laundry_id',$laundry_id)->get()->makehidden('translations');
    //   return Branchinfo::collection($branches);
       return response()->json(['branches'=>$branches]);
    }
}
