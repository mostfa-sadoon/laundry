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
use App\Models\Laundry\branchservice;
use App\Models\laundryservice\branchAdditionalservice;
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
        $branchid=$request->branch_id;
        DB::transaction(function()use($request,$branchid)
        {
           foreach($request->services as $service){
                foreach($service['categories'] as $category){
                     foreach($category['items'] as $item){
                        $baranchitem=branchitem::where('branch_id',$branchid)->where('category_id',$category['category_id'])->where('item_id',$item['item_id'])->first();
                       if($baranchitem==null){
                        $baranchitem= branchitem::create([
                            'category_id'=>$category['category_id'],
                            'item_id'=>$item['item_id'],
                            'branch_id'=>$branchid
                         ]);
                         $mainitem=Item::where('id',$item['item_id'])->first();
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
                $branchservices=branchservice::where('service_id',$service['service_id'])->where('branch_id',$branchid)->first();
                branchservice::create([
                    'service_id'=>$service['service_id'],
                    'branch_id'=>$branchid
                ]);
           }
           foreach($request->services as $service){
            foreach($service['categories'] as $category){
                 foreach($category['items'] as $item){
                    $branchitem=branchitem::where('branch_id',$branchid)->where('item_id',$item['item_id'])->first();
                    $serviceitemprice= Serviceitemprice::where('service_id',$service['service_id'])->where('branchitem_id',$branchitem->id)->first();
                    if($serviceitemprice==null){
                        $serviceitemprice=Serviceitemprice::create([
                            'branchitem_id'=>$branchitem->id,
                            'branch_id'=>$branchid,
                            'service_id'=>$service['service_id'],
                            'price'=>$item['price'],
                         ]);
                    }
                 }
             }
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
        $data=[];
        $data['status']=true;
        $data['message']="get additional services succesfully";
        $data['data']['aditionalservices']=$aditionalservices;
        return response()->json($data);
    }
    public function setaditionalserviceprice(Request $request){
        $branchid=$request->branch_id;
        // foreach($request->aditionalservices as $service){
        //     foreach($service['categories'] as $category){
        //          foreach($category['items'] as $item){
        //           $baranchitem= branchitem::where('item_id',$item['item_id'])->first();
        //          // return response()->json($baranchitem);

        //           if($baranchitem==null){
        //             return response()->json(['status'=>false,'message'=>'please set price of main services first'],403);
        //           }
        //           $vlaidtebranchitem=Serviceitemprice::where('branch_id',$branchid)->where('additionalservice_id',$service['additionalservice_id'])->where('branchitem_id',$baranchitem->id)->first();
        //            if($vlaidtebranchitem!=null){
        //                return response()->json(['status'=>false,'message'=>'this item already exist in this branch']);
        //            }
        //          }
        //         }
        //     }
        foreach($request->aditionalservices as $service){
            foreach($service['categories'] as $category){
                 foreach($category['items'] as $item){
                    $baranchitem= branchitem::where('item_id',$item['item_id'])->where('branch_id',$request->branch_id)->first();
                    if($baranchitem!=null){
                        $serviceitemprice=Serviceitemprice::create([
                            'branchitem_id'=>$baranchitem->id,
                            'branch_id'=>$branchid,
                            'additionalservice_id'=>$service['additionalservice_id'],
                            'price'=>$item['price'],
                         ]);

                         branchAdditionalservice::create([
                            'branchitem_id'=>$baranchitem->id,
                            'branch_id'=>$branchid,
                            'additionalservice_id'=>$service['additionalservice_id'],
                    ]);
                    }
                 }
            }
       }
       return response()->json(['status'=>true,'message'=>'aditional service prices added successfully']);
    }
    public function branchservices(Request $request){
      $lang=$request->header('lang');
      App::setLocale($lang);
      $branch_id=Auth::guard('branch-api')->user()->id;
       $branchservices=DB::table('brnachservices')
       ->join('services','services.id','=','brnachservices.service_id')
       ->join('servicetranslations','servicetranslations.service_id','=','services.id')->where('locale',$lang)
       ->select('status','brnachservices.service_id','name')->distinct()->get();
      if($branchservices->count()==0){
        return response()->json(['status'=>false,'message'=>'no services yet']);
      }
     $branchitem=branchitem::select('id')->listsTranslations('name')->with(['itemadditionalservice.additionalservice'])->get()->makehidden('translations');
      $data=[];
      $data['status']=true;
      $data['message']="get All services succesfully";
      $data['data']['branchservices']=$branchservices;
      $data['data']['branchitem']=$branchitem;
      return response()->json($data);
    }
    public function updateservicestatus(Request $request){
        $service_id=$request->service_id;
        $branch_id=Auth::guard('branch-api')->user()->id;
        $branchservice=branchservice::where('service_id',$service_id)->where('branch_id',$branch_id)->first();
        if($branchservice->status=='on'){
            $branchservice->update(['status'=>'off']);
        }else{
            $branchservice->update(['status'=>'on']);
        }
        $data['status']=true;
        $data['message']='service updated succefully';
        $data['data']['service']['id']=$service_id;
        $data['data']['service']['status']=$branchservice->status;
        return response()->json($data);
    }
    public function updateadditionalservicestatus(Request $request){
       // dd($request->all());
        $branch_id=Auth::guard('branch-api')->user()->id;
        $additionalservice_id=$request->additionalservice_id;
        $branchAdditionalservice= branchAdditionalservice::where('branchitem_id',$request->branchitem_id)->where('branch_id',$branch_id)->where('additionalservice_id',$additionalservice_id)->first();
        if($branchAdditionalservice->status=='on'){
            $branchAdditionalservice->update([
                'status'=>'off'
             ]);
        }else{
            $branchAdditionalservice->update([
                'status'=>'on'
             ]);
        }
        $data['status']=true;
        $data['message']='service updated succefully';
        $data['data']['branchitem_id']['id']=$request->branchitem_id;
        $data['data']['additionalservice']['status']=$branchAdditionalservice->status;
        return response()->json($data);
    }
    public $category=[];
    public function edit(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;

    }
}
