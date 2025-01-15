<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationCenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationCenterController extends Controller
{
    protected function getNotificationsUser(){
        $user = Auth::user()->usuario;

        $not_new= NotificationCenter::where("usuario_ad",$user)->where("read_status",0)->where("type","notification")->orderBy("id","desc")->get();
        // $not_new1= NotificationCenter::where("read_status",0)->where("archived",0)->where("type","system")->orderBy("id","desc")->get();
      
        $not_read= NotificationCenter::where("usuario_ad",$user)->where("read_status",1)->where("type","notification")->where("archived",0)->orderBy("id","desc")->get();
        $not_msg= NotificationCenter::where("type","system")->orderBy("id","desc")->get();
        $objArr=[];
        // $mergedArray = array_merge($not_new->toArray(), $not_new1->toArray());
        $data= array(
            "new" => $not_new,
            "read" => $not_read,
            "msg" => $not_msg
         );

        return response()->json($data, 200);    
    }

    protected function changeStatusNotification(Request $request){
        $not= NotificationCenter::find($request->id);

        if($request->type =="system"){
            $not->archived=1;
        }
        $not->read_status=1;
        $not->save();

        return response()->json(['success'=>'correcto'],200);    
    }

    protected function countNotification(){
		if(Auth::check()){
            $user = Auth::user()->usuario;
			$not_new= NotificationCenter::where("usuario_ad",$user)->where("read_status",0)->where("type","notification")->count();
			$not_new1= NotificationCenter::where("read_status",0)->where("type","system")->count();
			$tot=  $not_new +  $not_new1;

			$data= array(
				"new" => $not_new,
				"total" => $tot,
				"msg" => $not_new1
			 );

			return response()->json($data,200);
        }else{
            $data= array(
				"new" => 0,
				"total" => 0,
				"msg" => 0
			 );

			return response()->json($data,200);

        }
            
    }

    protected function viewAllNotifications(Request $request){
        $user = Auth::user()->usuario;

        $notifications = NotificationCenter::where("usuario_ad",$user)->where("read_status",0)
                                            ->where("type","notification")
                                            ->update([
                                                "read_status" => 1
                                            ]);

        $update = NotificationCenter::where("usuario_ad",$user)->where("read_status",0)
                                            ->where("type","system")
                                            ->update([
                                                "read_status" => 1
                                            ]);

        return response()->json(['success'=>'correcto'],200);    
    }

    public function addNotificationCenter($usuario_ad,$title,$msg,$type,$link,$icon,$priority){
        $not= new NotificationCenter();
        $not->usuario_ad=$usuario_ad;
        $not->title=$title;
        $not->message=$msg;
        $not->read_status=0;
        $not->type=$type;
        $not->link=$link;
        $not->icon=$icon;
        $not->priority=$priority;
        $not->archived=0;
        $not->save();

    }
}
