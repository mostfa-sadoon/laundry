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
         $services=service::select('id')->listsTranslations('name')->with('categories.items')->get();
        $data['services']= $services;
        return response()->json(['data'=>$data]);
    }

    public function setitemprice(Request $request){
        dd($request->all());
    }
}
