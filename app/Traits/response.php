<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait response
{
   public function response($status,$message,$data=null,$status_code=null){
        if($status_code==null)
        $status_code=200;
        if($data!=null){
            return response()->json([
                'status'=>$status,
                'message'=>$message,
                'data'=>$data,
            ],$status_code);
        }else{
            return response()->json([
                'status'=>$status,
                'message'=>$message,
            ],$status_code);
        }
   }
}
