<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\laundryservice\Service;
use App\Models\laundryservice\Category;
use App\Models\laundryservice\CategoryTranslations;
use Illuminate\Support\Facades\DB;
use App\Models\Laundry\Branchitem;
use App\Models\Laundry\BranchitemTranslation;
use App\Models\laundryservice\Item;
use App\Models\laundryservice\Additionalservice;
use App\Models\laundryservice\Serviceitemprice;
use App\Models\Laundry\branchservice;
use App\Models\laundryservice\branchAdditionalservice;
use App\Http\Resources\editservice\categoryresource;
use App\Http\Resources\editservice\serviceresource;
use App\Http\Resources\editservice\branchitem as branchitemresource;
use App\Models\Laundry\branch;
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
    // this function to get item in add view for mobile developer main service
    public function getcategoryitem(Request $request){
        $category_id=$request->category_id;
        $categoryitems=Item::where('category_id',$category_id)->get();
        $data['status']=true;
        $data['message']='get category items';
        $data['data']['categoryitems']=$categoryitems;
        return response()->json($data);
    }
    // this function to get item in add view for mobile developer additional service
    public function additionalserviceitem(Request $request){
        $category_id=$request->category_id;
        $branch_id=Auth::guard('branch-api')->user()->id;
        $categoryitems=Branchitem::select('id')->where('category_id',$category_id)->where('branch_id',$branch_id)->get();
        $data['status']=true;
        $data['message']='get category items';
        $data['data']['categoryitems']=$categoryitems;
        return response()->json($data);
    }
    public function setitemprice(Request $request){
        $branchid=$request->branch_id;
        $laundry_id=Auth::guard('laundry_api')->user()->id;
        $branch=branch::where('id',$branchid)->where('laundry_id',$laundry_id)->first();
        if($branch==null){
            return response()->json([
                'status'=>false,
                'message'=>'you haven not access in this branch or not found',
            ]);
        }
        DB::transaction(function()use($request,$branchid)
        {
        foreach($request->itemprices as $itemprice){
          $baranchitem=Branchitem::where('branch_id',$branchid)->where('category_id',$itemprice['category_id'])->where('item_id',$itemprice['item_id'])->first();
            if($baranchitem==null){
                $baranchitem= Branchitem::create([
                    'category_id'=>$itemprice['category_id'],
                    'item_id'=>$itemprice['item_id'],
                    'branch_id'=>$branchid
                    ]);
                    $mainitem=Item::where('id',$itemprice['item_id'])->first();
                    foreach(config('translatable.locales') as $locale){
                        BranchitemTranslation::create([
                            'name'=>$mainitem->translate($locale)->name,
                            'locale'=>$locale,
                            'branchitem_id'=>$baranchitem->id,
                        ]);
                    }
            }
        }
        foreach($request->services as $service){
            $branchservices=branchservice::where('service_id',$service)->where('branch_id',$branchid)->first();
            branchservice::create([
                'service_id'=>$service,
                'branch_id'=>$branchid
            ]);
        }
        foreach($request->itemprices as $itemprice){
            $Branchitem=Branchitem::where('branch_id',$branchid)->where('item_id',$itemprice['item_id'])->first();
            $serviceitemprice= Serviceitemprice::where('service_id',$itemprice['service_id'])->where('branchitem_id',$Branchitem->id)->first();
            if($serviceitemprice==null){
                $serviceitemprice=Serviceitemprice::create([
                    'branchitem_id'=>$Branchitem->id,
                    'category_id'=>$itemprice['category_id'],
                    'branch_id'=>$branchid,
                    'service_id'=>$itemprice['service_id'],
                    'price'=>$itemprice['price'],
                ]);
            }
        }
    });
        return response()->json(['status'=>true,'message'=>'service prices added successfully']);
    }


    public $item_id=[];
    public function getaditionalservices(Request $request){
        $lang=$request->header('lang');
        App::setLocale($lang);
        $baranchitems=Branchitem::select('item_id')->where('branch_id',$request->branch_id)->distinct()->get();
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
        $laundry_id=Auth::guard('laundry_api')->user()->id;
        $branch=branch::where('id',$branchid)->where('laundry_id',$laundry_id)->first();
        if($branch==null){
            return response()->json([
                'status'=>false,
                'message'=>'you haven not access in this branch or not found',
            ]);
        }
            foreach($request->itemprices as $itemprice){
                 $baranchitem= Branchitem::where('item_id',$itemprice['item_id'])->where('branch_id',$request->branch_id)->first();
                    if($baranchitem!=null){
                        $serviceitemprice=Serviceitemprice::where('branchitem_id',$baranchitem->id)
                        ->where('additionalservice_id',$itemprice['additionalservice_id'])
                        ->where('branch_id',$branchid)->first();
                        if($serviceitemprice==null){
                            $serviceitemprice=Serviceitemprice::create([
                                'branchitem_id'=>$baranchitem->id,
                                'branch_id'=>$branchid,
                                'additionalservice_id'=>$itemprice['additionalservice_id'],
                                'category_id'=>$itemprice['category_id'],
                                'price'=>$itemprice['price'],
                                ]);

                                branchAdditionalservice::create([
                                    'branchitem_id'=>$baranchitem->id,
                                    'branch_id'=>$branchid,
                                    'additionalservice_id'=>$itemprice['additionalservice_id'],
                            ]);
                        }



            }
            }
        return response()->json(['status'=>true,'message'=>'aditional service prices added successfully']);
 }
    public function branchservices(Request $request){
      $lang=$request->header('lang');
      App::setLocale($lang);
       $branch_id=Auth::guard('branch-api')->user()->id;
       $branch=branch::select('argent')->find($branch_id);
       $branchservices=DB::table('brnachservices')
       ->join('services','services.id','=','brnachservices.service_id')
       ->join('servicetranslations','servicetranslations.service_id','=','services.id')->where('locale',$lang)
       ->select('status','brnachservices.service_id','name')->where('branch_id', $branch_id)->distinct()->get();
      if($branchservices->count()==0){
        return response()->json(['status'=>false,'message'=>'no services yet']);
      }
     $Branchitem=Branchitem::select('id')
     ->whereHas('itemadditionalservice')
     ->with(['itemadditionalservice.additionalservice'])
    ->where('branch_id', $branch_id)
     ->get()->makehidden('translations');
      $data=[];
      $data['status']=true;
      $data['message']="get All services succesfully";
      $data['data']['argent']=$branch->argent;
      $data['data']['branchservices']=$branchservices;
      $data['data']['Branchitem']=$Branchitem;
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
        if($branchAdditionalservice!=null){
            if($branchAdditionalservice->status=='on'){
                $branchAdditionalservice->update([
                    'status'=>'off'
                 ]);
            }else{
                $branchAdditionalservice->update([
                    'status'=>'on'
                 ]);
            }
        }else{
            $data['status']=false;
            $data['message']='this item not fount';
            return response()->json($data);
        }



        $data['status']=true;
        $data['message']='service updated succefully';
        $data['data']['branchitem_id']['id']=$request->branchitem_id;
        $data['data']['additionalservice']['status']=$branchAdditionalservice->status;
        return response()->json($data);
    }

    // start edit api
    public $category=[];
    public $service_ids=[];
    public $additionalservice_ids=[];
    public function edit(Request $request){
      //   return response()->json($gf);
        $branch_id=Auth::guard('branch-api')->user()->id;
        $branchservices=Serviceitemprice::select('service_id')->where('branch_id',$branch_id)->where('service_id','!=',null)->distinct()->get();
        foreach($branchservices as $branchservice){
            array_push($this->service_ids,$branchservice->service_id);
        }
        $services=Service::select('id')->with(['categories'=>function($q)use($branch_id){
            $q->whereHas('branchitems',function($q){$q->where('branch_id',$branch_id);});
        }])->wherein('id',$this->service_ids)->select('id')->get()->makehidden(['created_at','updated_at']);
        $branchservices=Serviceitemprice::select('additionalservice_id')->where('branch_id',$branch_id)->where('additionalservice_id','!=',null)->distinct()->get();
        foreach($branchservices as $branchservice){
            array_push($this->additionalservice_ids,$branchservice->additionalservice_id);
        }
        $additionalservices=Additionalservice::with('categories')->wherein('id',$this->additionalservice_ids)->select('id')->get()->makehidden(['created_at','updated_at']);
        return[
            'status'=>true,
            'message'=>'get all pranch services successfully',
            'data'=>
            [
                'services'=> serviceresource::collection($services),
                'additionalservices'=>  serviceresource::collection($additionalservices),
            ]
        ];
        }
    public function getcategory(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $service_id=$request->service_id;
        $category_id=$request->category_id;
        $brnchitem=Branchitem::whereHas('branchitemprice',function($q)use($service_id,$branch_id,$category_id){
            $q->where('service_id',$service_id)->where('branch_id',$branch_id)->where('category_id',$category_id);
        })->with(['branchitemprice'=>function($q)use($service_id,$branch_id,$category_id){
            $q->where('service_id',$service_id)->where('branch_id',$branch_id)->where('category_id',$category_id)->get();
        }])->get();
        return[
        'status'=>true,
        'message'=>'get all pranch item price successfully',
        'data'=>
        [
            'branchitem'=> branchitemresource::collection($brnchitem),
        ]
        ];
    }
    public function getaditionalservicecategory(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $additionalservice_id=$request->additionalservice_id;
        $category_id=$request->category_id;
        $brnchitem=Branchitem::whereHas('branchitemprice',function($q)use($additionalservice_id,$branch_id,$category_id){
            $q->where('additionalservice_id',$additionalservice_id)->where('branch_id',$branch_id)->where('category_id',$category_id);
        })->with(['branchitemprice'=>function($q)use($additionalservice_id,$branch_id,$category_id){
            $q->where('additionalservice_id',$additionalservice_id)->where('branch_id',$branch_id)->where('category_id',$category_id)->get();
        }])->get();
        return[
        'status'=>true,
        'message'=>'get additional service price successfully',
        'data'=>
        [
            'branchitem'=> branchitemresource::collection($brnchitem),
        ]
        ];
    }
    public function updateprice(Request $request){
       foreach($request->branchitemprices as $itrmprice){
        Serviceitemprice::findorfail($itrmprice['item_price_id'])->update([
          'price'=>$itrmprice['price'],
        ]);
       }
       $data['status']=true;
       $data['message']="item updates succeffult";
       return response()->json($data);
    }
    // end edit api
    public function updateargent(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $branch=branch::find($branch_id);
        if($branch->argent==0){
           $branch->update([
              'argent'=>true
           ]);
        }else{
            $branch->update([
                'argent'=>false
             ]);
        }
        $data['status']=true;
        $data['message']="update argent succesfully";
        $data['data']['argent']=$branch->argent;
        return response()->json($data);
    }
}
