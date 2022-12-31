<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Traits\response;
use Auth;
use Validator;
use Hash;
class ProfileController extends Controller
{
    //
    use response;
    private UserRepositoryInterface $UserRepository;
    public function __construct(UserRepositoryInterface $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }
    public function edit(Request $request){
        $user_id=Auth::guard('user_api')->user()->id;
        $user= $this->UserRepository->userinfo($user_id);
        unset($user['phone']);unset($user['country_code']);
        return $this->response(true,'get data success',$user);
    }
    public function editphone(Request $request){
        $user_id=Auth::guard('user_api')->user()->id;
        $user= $this->UserRepository->userinfo($user_id);
        unset($user['name']);unset($user['email']);
        return $this->response(true,'get data success',$user);
    }
    public function update(Request $request){
        $user_id=Auth::guard('user_api')->user()->id;
        $validator =Validator::make($request->all(), [
            'name'=>'required|unique:users,name,'.$user_id,
            'email'=>'required|unique:users,email,'.$user_id,
          ]);
          if ($validator->fails()) {
            return $this->response(false,$validator->messages()->first(),null,401);
          }
        $user=$this->UserRepository->updateuser($request->all(),$user_id);
        if($user==true)
        return $this->response(true,'data updated successfully',null);
        if($user==false)
        return $this->response(false,'some thing is wrong',null,401);
    }
    public function updatepassword(Request $request){
        $user_id=Auth::guard('user_api')->user()->id;
        $validator =Validator::make($request->all(), [
            'password'=> 'required|min:6|max:50|confirmed',
            'password_confirmation' => 'required|max:50|min:6',
          ]);
          if ($validator->fails()) {
            return $this->response(false,$validator->messages()->first(),null,401);
          }
        $data=$request->all();
        unset($data['password_confirmation']);
        $data['password']= Hash::make($request->password);
        $user=$this->UserRepository->updateuser($data,$user_id);
        if($user==true)
        return $this->response(true,'data updated successfully',null);
        if($user==false)
        return $this->response(false,'some thing is wrong',null,401);
    }
    public function updatephone(Request $request){
        $user_id=Auth::guard('user_api')->user()->id;
        $validator =Validator::make($request->all(), [
            'phone'=> 'required',
            'country_code' => 'required',
          ]);
        if ($validator->fails()) {
        return $this->response(false,$validator->messages()->first(),null,401);
        }
        $user=$this->UserRepository->updatephone($user_id);
        return $this->response(true,'go to authntication request',$user);
    }
}
