<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Traits\response;
use Auth;
class AdressController extends Controller
{
    //
    use response;
    private UserRepositoryInterface $UserRepository;
    public function __construct(UserRepositoryInterface $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }
    public function createadress(Request $request){
     $user_id=Auth::guard('user_api')->user()->id;
     $adress= $this->UserRepository->addaddress($request,$user_id);
     if(is_array($adress)){
        return $this->response(false,$adress['message']);
     }
     return $this->response(true,'adress added successfuly');
    }
    public function deleteadress(Request $request){
        $adress_id=$request->adress_id;
        $adress= $this->UserRepository->deleteadress($adress_id);
        if(is_array($adress)){
            return $this->response(false,$adress['message']);
         }
        return $this->response(true,'adress deleted successfuly');
    }
}