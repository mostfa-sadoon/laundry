<?php

namespace App\Repositories;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\User\Adress;
use Validator;
use Hash;
use DB;
use Illuminate\Support\Str;
class UserRepository implements UserRepositoryInterface
{

    public function signin($request){
        $validator =Validator::make($request->all(), [
            'country_code'=>'required',
            'password'=> 'required|min:6|max:50',
            'phone'=>'required',
          ]);
          if ($validator->fails()) {
           return [
               'message'=>$validator->messages()->first()
           ];
        }
        $user=User::where('phone',$request->phone)->where('country_code',$request->country_code)->first();
        if($user==null)
        return ['message' => 'your phone or password is  wrong'];
        if($user->verified==false){
            return ['message' => 'please verify your phone first'];
        }
        if (!$token = auth()->guard('user_api')->tokenById($user->id)) {
            return ['message' => 'token is false'];
           }
          return $token;
    }
    public function createUser($request)
    {
        $validator =Validator::make($request->all(), [
            'name'=>'required|unique:users',
            'email'=>'required|unique:users',
            'country_code'=>'required',
            'password'=> 'required|min:6|max:50|confirmed',
            'password_confirmation' => 'required|max:50|min:6',
            'phone'=>'required|unique:users',
          ]);
          if ($validator->fails()) {
           return [
               'message'=>$validator->messages()->first()
           ];
        }
        $data=$request->all();
        $data['otp']=1234;
        $data['password']= Hash::make($request->password);
        unset($data['password_confirmation']);
        $user= User::create($data);

    }
    public function verifyphone($request){
       $user=User::where('phone',$request->phone)->where('country_code',$request->country_code)->first();
       if($user=null)
       return false;
       if($request->otp==$user->otp){
          $user->update([
            'verified'=>true,
            'otp'=>null
          ]);
       }else{
        return false;
       }
       return true;
    }
    public function addaddress($request,$userid){
        $validator =Validator::make($request->all(), [
            'lat'=>'required',
            'long'=>'required',
            'city'=>'required',
            'street'=>'required'
          ]);
          if ($validator->fails()) {
           return [
               'message'=>$validator->messages()->first()
           ];
          }
       $data=$request->all();
       //unset($data['name'],$data['email'],$data['country_code'],$data['phone'],$data['password_confirmation'],$data['password']);
       $data['user_id']=$userid;
       Adress::create($data);
    }
    public function deleteadress($address_id){
       $adress=Adress::find($address_id)->delete();
       if($adress==null){
        return ['message'=>'this adress not found'];
       };
    }
    public function userinfo($id){
       try{
        DB::beginTransaction();
        $user=User::select('name','email','phone','country_code')->find($id);
        return $user;
        DB::commit();
        }catch(\Exception $ex){
        DB::rollback();
        return false;
        }
    }
    public function updateuser($request,$id){
        try{
        DB::beginTransaction();
        $data=$request;
        $user=User::find($id);
        $user->update($data);
        return true;
        DB::commit();
        }catch(\Exception $ex){
        DB::rollback();
        return false;
        }
    }
    public function updatephone($id){
      $user=User::find($id);
      $user->update([
        'verificationcode'=>Str::random(40).now().$user->id,
      ]);
      return ['verificationcode'=>$user->verificationcode];
    }
}
