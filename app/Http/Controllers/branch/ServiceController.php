<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\laundryservice\service;
use App\Models\laundryservice\Category;
use App\Models\laundryservice\CategoryTranslations;
use Illuminate\Support\Facades\DB;
use App\Models\Laundry\branchitem;
use App\Models\Laundry\branchitemTranslation;
use App\Models\laundryservice\Item;
use App\Models\laundryservice\Aditionalservice;
use App\Models\laundryservice\Serviceitemprice;
use App;
use Auth;


class ServiceController extends Controller
{
    //
    public function getservices(Request $request){
         $lang=$request->header('lang');
         App::setLocale($lang);
         $data=[];
         $services=service::select('id')->listsTranslations('name')->with('categories.items')->get();
        $data['services']= $services;
        return response()->json(['message'=>'get services succefully','data'=>$data]);
    }
    public function setitemprice(Request $request){
       // dd($request->services[0]->categories[0]);
        $branchid=Auth::guard('branch-api')->user()->id;
        $mainitem=Item::where('id',1)->first();
      //  return response()->json([$mainitem->translate('en')->name]);
        DB::transaction(function()use($request,$branchid)
        {
           foreach($request->services as $service){
                foreach($service['categories'] as $category){
                     foreach($category['items'] as $item){
                       $baranchitem= branchitem::create([
                            'category_id'=>$category['category_id'],
                            'item_id'=>$item['item_id'],
                            'service_id'=>$service['service_id'],
                            'price'=>$item['price'],
                            'branch_id'=>$branchid
                         ]);
                         $serviceitemprice=Serviceitemprice::create([
                            'branchitem_id'=>$baranchitem->id,
                            'branch_id'=>$branchid,
                            'service_id'=>$service['service_id'],
                            'price'=>$item['price'],
                         ]);
                         $mainitem=Item::where('id',1)->first();
                         foreach(config('translatable.locales') as $locale){
                                branchitemTranslation::create([
                                    'name'=>$mainitem->translate($locale)->name,
                                    'locale'=>$locale,
                                    'branchitem_id'=>$baranchitem->id,
                                ]);
                            }
                     }
                }
           }
        });
        return response()->json(['message'=>'service prices added successfully']);
       // return $this->returnData('laundry_id', $laundry->id, $msg = "laundery added succesffuly",200);
    }
    public function getaditionalservices(Request $request){
       // dd($request->all());
        $branchid=Auth::guard('branch-api')->user()->id;
        $baranchitems=branchitem::select('id')->where('branch_id',$branchid)->get()->makehidden('translations');
        $data=[];
       // $data['data']=$baranchitems;
        //  foreach($baranchitems as $baranchitem){
        //     $data['data']->item= $baranchitem;
        //     $data['data']->item= $baranchitem;
        //  }
        $data['data']['items']=$baranchitems;
        return response()->json($data);
        return response()->json(['baranchitems'=>$baranchitems]);
        $aditionalservice=Aditionalservice::listsTranslations()->get();

        return response()->json(['baranchitems'=>$baranchitems,'aditionalservice'=>$aditionalservice]);
    }
}
