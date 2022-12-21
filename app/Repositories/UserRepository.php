<?php

namespace App\Repositories;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\User\Adress;
use Validator;
use Hash;

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
        $data['otp']=1234;
        $data['password']= Hash::make($request->password);
        unset($data['password_confirmation'],$data['city'],$data['district'],$data['street'],$data['building'],$data['floor'],$data['flat'],$data['lat'],$data['long']);
        $user= User::create($data);
        $this->addaddress($request,$user->id);
    }
    public function verifyphone($request){
       $user=User::where('phone',$request->phone)->where('country_code',$request->country_code)->first();
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
       unset($data['name'],$data['email'],$data['country_code'],$data['phone'],$data['password_confirmation'],$data['password']);
       $data['user_id']=$userid;
       Adress::create($data);
    }
    public function deleteadress($address_id){
       $adress=Adress::find($address_id)->delete();
       if($adress==null){
        return ['message'=>'this adress not found'];
       };
    }
}
