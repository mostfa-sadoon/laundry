<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\laundryservice\service;
use App\Models\laundryservice\Category;
use App\Models\laundryservice\CategoryTranslations;
use Illuminate\Support\Facades\DB;
use App;

class ServiceController extends Controller
{
    //
    public function getservices(Request $request){
         $lang=$request->header('lang');
         App::setLocale($lang);
         $data=[];
         $services=service::select('id')->listsTranslations('name')->with('categories')->get();
         //dd($services);
        //  $services=service::
        //  join('servicetranslations','services.id','=','servicetranslations.service_id')
        // ->join('categoryservices','services.id','=','categoryservices.service_id')
        // ->join('categories','categoryservices.category_id','=','categories.id')
        // ->join('categorytranslations','categorytranslations.category_id','=','categories.id')
        // ->select('services.id','servicetranslations.name as servicename','categorytranslations.name as categorynamename')
        // ->distinct()
        // ->get();
        $data['services']= $services;
        return response()->json(['data'=>$data]);
    }
}
