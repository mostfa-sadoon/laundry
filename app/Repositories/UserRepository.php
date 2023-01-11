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
    function uploadImage($photo_name, $folder)
    {
        $image = $photo_name;
        $image_name = time() . '' . $image->getClientOriginalName();
        $destinationPath = public_path($folder);
        $image->move($destinationPath, $image_name);
        return $image_name;
    }
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
        $data['verified']=true;
        $user= User::create($data);
        $credentials = ['phone'=>$user->phone,
        'password'=>$request->password];
         if (!$token = auth()->guard('user_api')->attempt($credentials)) {
         return response()->json(['error' => 'Your user phone or password maybe incorrect, please try agian'], 401);
         }
         return $token;
    }
    public function verifyphone($request){
       $user=User::where('phone',$request->phone)->where('country_code',$request->country_code)->first();
       if($user==null)
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
       $data=$request->all();
       //unset($data['name'],$data['email'],$data['country_code'],$data['phone'],$data['password_confirmation'],$data['password']);
       $data['user_id']=$userid;
       $address=Adress::where('user_id',$userid)->get();
       if($address!=null){
        foreach($address as $addres)
           $addres->update([
             'curent'=>0
           ]);
       }
       Adress::create($data);
    }
    public function updateaddress($request,$userid){
        $address_id=$request->address_id;
        $address=Adress::where('user_id',$userid)->get();
        if($address!=null){
         foreach($address as $addres)
            $addres->update([
              'curent'=>0
            ]);
        }
        $adress=Adress::find($address_id);
        if($adress==null){
            return ['message'=>'this adress not found'];
        };
        $data=$request->all();
        unset($data['address_id']);
        $adress->update($data);
        return true;
    }
    public function deleteadress($address_id){
       $adress=Adress::find($address_id)->delete();
       if($adress==null){
        return ['message'=>'this adress not found'];
       };
    }
    public function getaddresses($user){
        $adresses=Adress::where('user_id',$user->id)->get();
        if($adresses==null)
        return false;
        return $adresses;
    }
    public function userinfo($id){
       try{
        DB::beginTransaction();
        $user=User::select('name','email','phone','img','country_code')->find($id);
        return $user;
        DB::commit();
        }catch(\Exception $ex){
        DB::rollback();
        return false;
        }
    }
    public function updateuser($request,$id){
        $user=User::find($id);
        if($request->hasFile('img')){
            $image_name = $this->uploadImage($request->file('img'), 'uploads/users/img');
        }else{
            $image_name=$user->img;
        }
        $data=$request->all();
        $data['img']=$image_name;

        $user->update($data);
        return true;
    }
    public function updatephone($id){
      $user=User::find($id);
      $user->update([
        'verificationcode'=>Str::random(40).now().$user->id,
      ]);
      return ['verificationcode'=>$user->verificationcode];
    }
}
