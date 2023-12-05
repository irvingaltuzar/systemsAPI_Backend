<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Auth\LoginController;
use App\Models\DmiRh\DmirhPersonalTime as PersonalTime;
use App\Models\DmiRh\DmirhPersonalTimeDetail as PersonalTimeDetail;
use App\Models\DmiRh\DmirhPersonalTimeComment as PersonalTimeComment;
use App\Models\DmiRh\DmirhCatTimeStatus as CatTimeStatus;
use App\Models\DmiRh\DmirhWorkSchedule as WorkSchedule;
class CambioHorarioController extends Controller
{


    protected function addCambioHorarioPersonal(Request $request){

      // try {
        $datos= $this->validate(request(),[
          'jornada' => 'required',
          'HourEntrance' => 'required',
          'HourFood1' => 'required',
          'HourFood2' => 'required',
          'HourExit' => 'required',
          ]);
          $nom_usuario=auth()->user()->usuario;
          try {
            $upd= PersonalTime::where("active",1)->where("user",$nom_usuario)->where("dmirh_cat_time_status_id",2)->first();
            if($upd !=""){
            $usr= PersonalTime::find($upd->id);
            $usr->active=0;
            $usr->save();
            }
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
          }


          $personaltime = new PersonalTime();
          $personaltime->user = $nom_usuario;
          $personaltime->dmirh_cat_time_status_id = 2;
          $personaltime->active = 1;
          $personaltime->save();
          $personaltimeId=DB::getPdo()->lastInsertId();

          $arrJornada= explode(",",$datos["jornada"]);

          foreach ( $arrJornada as $value) {
            $personaltimeDetail = new PersonalTimeDetail();
            $personaltimeDetail->dmirh_personal_time_id= $personaltimeId;

            if($value =="Lunes"){
            $personaltimeDetail->week_day= 1;
            $personaltimeDetail->entry_hour= $request["HourEntrance"];
            $personaltimeDetail->exit_food_hour= $request["HourFood1"];
            $personaltimeDetail->entry_food_hour= $request["HourFood2"];
            $personaltimeDetail->exit_hour= $request["HourExit"];
            }else if($value =="Martes"){
              $personaltimeDetail->week_day= 2;
              $personaltimeDetail->entry_hour= $request["HourEntrance"];
            $personaltimeDetail->exit_food_hour= $request["HourFood1"];
            $personaltimeDetail->entry_food_hour= $request["HourFood2"];
            $personaltimeDetail->exit_hour= $request["HourExit"];
            }else if($value =="Miercoles"){
              $personaltimeDetail->week_day= 3;
              $personaltimeDetail->entry_hour= $request["HourEntrance"];
              $personaltimeDetail->exit_food_hour= $request["HourFood1"];
              $personaltimeDetail->entry_food_hour= $request["HourFood2"];
              $personaltimeDetail->exit_hour= $request["HourExit"];
            }else if($value =="Jueves"){
              $personaltimeDetail->week_day= 4;
              $personaltimeDetail->entry_hour= $request["HourEntrance"];
              $personaltimeDetail->exit_food_hour= $request["HourFood1"];
              $personaltimeDetail->entry_food_hour= $request["HourFood2"];
              $personaltimeDetail->exit_hour= $request["HourExit"];
            }else if($value =="Viernes"){
              $personaltimeDetail->week_day= 5;
              $personaltimeDetail->entry_hour= $request["HourEntrance"];
              $personaltimeDetail->exit_food_hour= $request["HourFood1"];
              $personaltimeDetail->entry_food_hour= $request["HourFood2"];
              $personaltimeDetail->exit_hour= $request["HourExit"];
            }else if($value =="Sabado"){
              $personaltimeDetail->week_day= 6;
              $personaltimeDetail->entry_hour= $request["HourEntrance"];
              $personaltimeDetail->exit_food_hour= $request["HourFood1"];
              $personaltimeDetail->entry_food_hour= $request["HourFood2"];
              $personaltimeDetail->exit_hour= $request["HourExit"];
            }else if($value =="Domingo"){
              $personaltimeDetail->week_day= 7;
              $personaltimeDetail->entry_hour= $request["HourEntrance"];
              $personaltimeDetail->exit_food_hour= $request["HourFood1"];
              $personaltimeDetail->entry_food_hour= $request["HourFood2"];
              $personaltimeDetail->exit_hour= $request["HourExit"];
            }
            $personaltimeDetail->save();
          }

          return response()->json(['success'=>'Se guardo exitosamente.'],200);


    }

    protected function updateCambioHorarioPersonal(Request $request){

      // try {
        $datos= $this->validate(request(),[
          'jornada' => 'required',
          'HourEntrance' => 'required',
          'HourFood1' => 'required',
          'HourFood2' => 'required',
          'HourExit' => 'required',
          'timeId'=> 'required',
          ]);
          $arrJornadaToInt=[];
          // $personaltime= PersonalTime::find($datos["timeId"]);
          // $personaltime->user = $nom_usuario;
          // $personaltime->dmirh_cat_time_status_id = 2;
          // $personaltime->save();
          $arrJornada= explode(",",$datos["jornada"]);

             //consulta de todos los elementos actuales horarios details
             $horarios= DB::table('dmirh_personal_time_detail')->where('dmirh_personal_time_id',$request["timeId"])->where('deleted',0)->get();

             //Agregar jornada to week_day
         for ($i = 0; $i <count($arrJornada); $i++) {
          if($arrJornada[$i] =="Lunes"){
            array_push($arrJornadaToInt,1);
            }else if($arrJornada[$i] =="Martes"){
              array_push($arrJornadaToInt,2);
            }else if($arrJornada[$i] =="Miercoles"){
              array_push($arrJornadaToInt,3);
            }else if($arrJornada[$i] =="Jueves"){
              array_push($arrJornadaToInt,4);
            }else if($arrJornada[$i] =="Viernes"){
              array_push($arrJornadaToInt,5);
            }else if($arrJornada[$i] =="Sabado"){
              array_push($arrJornadaToInt,6);
            }else if($arrJornada[$i] =="Domingo"){
              array_push($arrJornadaToInt,7);

            }
          }
          for ($i = 0; $i <count($arrJornadaToInt); $i++) {
                $hor= DB::table('dmirh_personal_time_detail')->where('dmirh_personal_time_id',$request["timeId"])->where('week_day',$arrJornadaToInt[$i])->first();
                 if(!$hor){
                 $personaltimeDetail= new PersonalTimeDetail();
                 $personaltimeDetail->dmirh_personal_time_id= $request["timeId"];
                 $personaltimeDetail->week_day=$arrJornadaToInt[$i];
                 $personaltimeDetail->entry_hour= $request["HourEntrance"];
                 $personaltimeDetail->exit_food_hour= $request["HourFood1"];
                 $personaltimeDetail->entry_food_hour= $request["HourFood2"];
                 $personaltimeDetail->exit_hour= $request["HourExit"];
                 $personaltimeDetail->deleted=0;
                 $personaltimeDetail->save();
                 }else{
                  $personaltimeDetail= PersonalTimeDetail::find($hor->id);
                  $personaltimeDetail->entry_hour= $request["HourEntrance"];
                  $personaltimeDetail->exit_food_hour= $request["HourFood1"];
                  $personaltimeDetail->entry_food_hour= $request["HourFood2"];
                  $personaltimeDetail->exit_hour= $request["HourExit"];
                  $personaltimeDetail->deleted=0;
                  $personaltimeDetail->save();

                 }
             }

             //Borrar horario detail que no exista
         foreach($horarios as $hora){

             $day=$hora->week_day;
             if(!in_array($day, $arrJornadaToInt)){
                 $raw= PersonalTimeDetail::find($hora->id);
                 $raw->deleted=1;
                 $raw->save();
             }
         }

          return response()->json(['success'=>'Se actualizo exitosamente.'],200);


    }

    protected function getHourEntrance(){
      if(Auth::check()){
        $res = WorkSchedule::where("type","Entrada")->get();

        return response()->json($res, 200);
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }
    protected function getHourFood(){
      if(Auth::check()){
      $res = WorkSchedule::where("type","Comida")->get();

      return response()->json($res, 200);
    }else{

      return response()->json(['error'=>'No tienes Sesion'],200);
      }
  }

  protected function getHorarioPendiente(){
    if(Auth::check()){
    $usuario =auth()->user()->usuario;

    $res= DB::table('dmirh_personal_time')
    ->join('dmirh_personal_time_detail', 'dmirh_personal_time.id', '=', 'dmirh_personal_time_detail.dmirh_personal_time_id')
    ->join('cat_time_status', 'dmirh_personal_time.dmirh_cat_time_status_id', '=', 'cat_time_status.id')
    ->where('dmirh_personal_time.active',1)->where('dmirh_personal_time.user',$usuario)
    ->where(function ($query) {
      $query->where('cat_time_status.description', '=', 'Pendiente')
            ->orWhere('cat_time_status.description', '=', 'Autorizado');
  })->get();
    // $res = DB::select("Select * from dmirh_work_schedules where type='Comida'");

    return response()->json($res, 200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
}
}
