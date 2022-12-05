<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification\drivernotifytype;
use Auth;
use App;

class NotificationController extends Controller
{
    //
    use response;
    public function getnotification(Request $request){
        $driver_id=Auth::guard('driver_api')->user()->id;
        $lang=$request->header('lang');
        App::setLocale($lang);
        $notifications=DB::table('notificationtypes')
        ->join('notificationtypetranslations','notificationtypetranslations.notificationtype_id','=','notificationtypes.id')
        ->join('drivernotifytype','drivernotifytype.notificationtype_id','=','notificationtypes.id')
        ->select('drivernotifytype.id as id','notificationtypetranslations.name','drivernotifytype.status')
        ->where('locale',$lang)->where('driver_id',$driver_id)->get();
        $data['data']['notifications']=$notifications;
        return $this->response(true,'get avilable notification successfully',$data);
    }
    public function updatenotification(Request $request){
        $notification_id=$request->notification_id;
        $notification= drivernotifytype::find($notification_id);
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
