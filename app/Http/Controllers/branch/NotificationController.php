<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\response;
use Illuminate\Http\Request;
use App\Models\Notification\branchnotifytype;
use Auth;
use App;

class NotificationController extends Controller
{
    //
    use response;
    public function getnotification(Request $request){
        $branch_id=Auth::guard('branch-api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $notifications=DB::table('notificationtypes')
        ->join('notificationtypetranslations','notificationtypetranslations.notificationtype_id','=','notificationtypes.id')
        ->join('branchnotifytype','branchnotifytype.notificationtype_id','=','notificationtypes.id')
        ->select('branchnotifytype.id as id','notificationtypetranslations.name','branchnotifytype.status')
        ->where('locale',$lang)
        ->where('branch_id',$branch_id)
        ->get()->distinct();
        $data['data']['notifications']=$notifications;
        return $this->response(true,'get avilable driver successfully',$data);
    }
    public function updatenotification(Request $request){
        $notification_id=$request->notification_id;
        $notification= branchnotifytype::find($notification_id);
        if($notification->status==true){
            $notification->update([
                'status'=>false
            ]);
        }else{
            $notification->update([
                'status'=>true
            ]);
        }
        return $this->response(true,'notification updated successfully');
    }
}
