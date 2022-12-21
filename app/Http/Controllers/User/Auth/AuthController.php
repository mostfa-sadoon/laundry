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
           return $this->response(false,$user['message']);
        }
        $data['token']=$user;
        return $this->response(true,'you logged in successfully',$data);
    }
    public function register(Request $request){
        $user= $this->UserRepository->createUser($request);
        if(is_array($user)){
           return $this->response(false,$user['message']);
        }
        return $this->response(true,'verfy phone number please');
    }
    public function verifyphone(Request $request){
        $verifyphone=$this->UserRepository->verifyphone($request);
        if($verifyphone==false){
            return $this->response(false,'otp is false');
        }
        return $this->response(true,'account verfied');
    }
}
