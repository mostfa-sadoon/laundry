<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\laundryservice\Service;
use App\Models\laundryservice\Category;
use App\Models\laundryservice\CategoryTranslations;
use Illuminate\Support\Facades\DB;
use App\Models\Laundry\branchitem;
use App\Models\Laundry\branchitemTranslation;
use App\Models\laundryservice\Item;
use App\Models\laundryservice\Additionalservice;
use App\Models\laundryservice\Serviceitemprice;
use App\Models\Laundry\Pranchservice;
use App;
use Auth;


class ServiceController extends Controller
{
    //
    public function getservices(Request $request){
         $lang=$request->header('lang');
         App::setLocale($lang);
         $data=[];
         $services=Service::select('id')->listsTranslations('name')->with('categories.items')->get();
         $data['services']= $services;
         return response()->json(['status'=>true,'message'=>'get services succefully','data'=>$data]);
    }
    public function setitemprice(Request $request){
       // dd($request->services[0]->categories[0]);
        $branchid=$request->pranch_id;
       // $mainitem=Item::where('id',1)->first();
      //  return response()->json([$mainitem->translate('en')->name]);

      foreach($request->services as $service){
        foreach($service['categories'] as $category){
             foreach($category['items'] as $item){
              $vlaidtebranchitem=branchitem::where('branch_id',$branchid)->where('service_id',$service['service_id'])->where('item_id',$item['item_id'])->first();
               if($vlaidtebranchitem!=null){
                   return response()->json(['status'=>false,'message'=>'this item already exist in this branch']);
               }
             }
            }
        }
        DB::transaction(function()use($request,$branchid)
        {
           foreach($request->services as $service){
                foreach($service['categories'] as $category){
                     foreach($category['items'] as $item){
                      $vlaidtebranchitem=branchitem::where('branch_id',$branchid)->where('service_id',$service['service_id'])->where('item_id',$item['item_id'])->first();
                       if($vlaidtebranchitem!=null){
                           return response()->json(['status'=>false,'message'=>'this item already exist']);
                       }
                       $baranchitem= branchitem::create([
                            'category_id'=>$category['category_id'],
                            'item_id'=>$item['item_id'],
                            'service_id'=>$service['service_id'],
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
                Pranchservice::create([
                    'service_id'=>$service['service_id'],
                    'branch_id'=>$branchid
                ]);
           }
        });
        return response()->json(['status'=>true,'message'=>'service prices added successfully']);
    }
    public $item_id=[];
    public function getaditionalservices(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $baranchitems=branchitem::select('item_id')->where('branch_id',$request->branch_id)->distinct()->get();
        foreach($baranchitems as $baranchitem)
        {
            array_push($this->item_id,$baranchitem->item_id);
        }
        $aditionalservices=Additionalservice::select('id')->listsTranslations('name')->with(['categories'=>function($q){
            $q->with(['items'=>function($q){
               $q->wherein('id',$this->item_id)->get();
            }])->get();
        }])->get()->makehidden('translations');
        return response()->json($aditionalservices);
    }

    public function setaditionalserviceprice(Request $request){
        $branchid=$request->pranch_id;
        foreach($request->aditionalservices as $service){
            foreach($service['categories'] as $category){
                 foreach($category['items'] as $item){
                  $vlaidtebranchitem=Serviceitemprice::where('branch_id',$branchid)->where('additionalservice_id',$service['additionalservice_id'])->where('item_id',$item['item_id'])->first();
                   if($vlaidtebranchitem!=null){
                       return response()->json(['status'=>false,'message'=>'this item already exist in this branch']);
                   }
                 }
                }
            }
        foreach($request->aditionalservices as $service){
            foreach($service['categories'] as $category){
                 foreach($category['items'] as $item){
                    $baranchitem= branchitem::where('item_id',$item['item_id'])->first();
                    if($baranchitem!=null){
                        $serviceitemprice=Serviceitemprice::create([
                            'branchitem_id'=>$baranchitem->id,
                            'branch_id'=>$branchid,
                            'additionalservice_id'=>$service['additionalservice_id'],
                            'price'=>$item['price'],
                         ]);
                    }
                 }
            }
       }
       return response()->json(['status'=>true,'message'=>'aditional service prices added successfully']);
    }

}
