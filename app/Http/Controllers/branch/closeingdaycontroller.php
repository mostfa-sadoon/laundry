<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\closeingday\Closeingday;
use App;

class closeingdaycontroller extends Controller
{
    //
    public function getcloseingday(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
      $Closeingdaies=Closeingday::get();
      return response()->json(['Closeingdaies'=>$Closeingdaies]);
    }
}
