<?php

namespace App\Http\Controllers\User\Auth;
use App\Interfaces\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\response;
use Auth;
use App;
use Hash;
class AuthController extends Controller
{
    //
    use response;
    private UserRepositoryInterface $UserRepository;
    public function __construct(UserRepositoryInterface $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }
    public function signin(Request $request){
        $user= $this->UserRepository->signin($request);
        if(is_array($user)){
           return $this->response(false,$user['message'],null,401);
        }
        $data['token']=$user;
        return $this->response(true,'you logged in successfully',$data);
    }
    public function register(Request $request){
        $token= $this->UserRepository->createUser($request);
        if(is_array($token)){
           return $this->response(false,$token['message']);
        }
        $data['token']=$token;
        return $this->response(true,'accout created successfuly go to login',$data);
    }
    public function verifyphone(Request $request){
        $verifyphone=$this->UserRepository->verifyphone($request);
        if($verifyphone==false){
            return $this->response(false,'otp is false');
        }
        return $this->response(true,'account verfied');
    }
}
