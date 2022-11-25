<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait response
{
   public function response($status,$message,$data=null){
    if($data!=null){
        return response()->json([
            'status'=>$status,
            'message'=>$message,
            'data'=>$data,
           ]);
    }else{
        return response()->json([
            'status'=>$status,
            'message'=>$message,
           ]);
    }

   }
}
