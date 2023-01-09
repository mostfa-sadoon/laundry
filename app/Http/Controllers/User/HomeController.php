<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Traits\response;
use App;
class HomeController extends Controller
{
    //
    use response;
    public function getslider(Request $request){
         $lang=$request->header('lang');
         App::setLocale($lang);
         $sliders=Slider::select('id')->get();
         $data['slider']=$sliders;
         return $this->response(true,'get sliderssuccessfully',$data);
    }
}
