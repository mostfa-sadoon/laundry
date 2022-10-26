<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\laundryservice\service;
use App\Models\laundryservice\Category;
use App\Models\laundryservice\CategoryTranslations;
use Illuminate\Support\Facades\DB;
use App\Models\laundry\branchitem;
use App\Models\laundry\branchitemTranslation;
use App\Models\laundryservice\Item;
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
        return response()->json(['data'=>$data]);
    }

    public function setitemprice(Request $request){
       // dd($request->services[0]->categories[0]);
        $branchid=Auth::guard('branch-api')->user()->id;

        $mainitem=Item::where('id',1)->first();
      //  return response()->json([$mainitem->translate('en')->name]);



        DB::transaction(function()use($request)
        {
           foreach($request->services as $service){
                foreach($service['categories'] as $category){
                     foreach($category['items'] as $item){
                       $baranchitem= branchitem::create([
                            'category_id'=>$category['category_id'],
                            'item_id'=>$item['item_id'],
                            'service_id'=>$service['service_id'],
                            'price'=>$item['price'],
                         ]);
                         $mainitem=Item::where('id',1)->first();
                         foreach(config('translatable.locales') as $locale){
                                branchitemTranslation::create([
                                    'name'=>$mainitem->translate($locale)->name,
                                    'locale'=>$locale,
                                    'branch_item_id'=>$baranchitem->id,
                                ]);
                            }
                     }
                }
           }
        });
        return response()->json(['message'=>'service prices added successfully']);
       // return $this->returnData('laundry_id', $laundry->id, $msg = "laundery added succesffuly",200);
    }
}
