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
use App\Models\PersonalIntelisis as PersonalIntelisis;
use App\Models\DmiControlProcedureValidation;
use App\Services\SendEmailService;

class HorariosPersonalController extends Controller
{

  private $sendEmail;

	public function __construct(SendEmailService $sendEmail)
	{
        //$this->middleware('guest',['only'=>'ShowLogin']);
		$this->sendEmail = $sendEmail;
	}

    protected function getHorariosPersonal(){
        $usuario =auth()->user()->usuario;
        // $res= DB::select("select * from personal_intelisis where status='ALTA' and usuario_ad IS NOT NULL order by name");
        $res= PersonalIntelisis::where("status","ALTA")->whereNotNull("usuario_ad")->orderBy("name")->get();
        $objHorarios=[];

        foreach($res as $pers){
          $lunes=array();
          $martes=array();
          $miercoles=array();
          $jueves=array();
          $viernes=array();
          $sabado=array();
          $domingo=array();
          $status="";
          $vigencia="";
//             $res2= DB::select("
//             select * from dmirh_personal_time as a
//             INNER JOIN (select user,max(created_at) as creado
//                                  from dmirh_personal_time where dmirh_cat_time_status_id=1
//                                  group by user) max_user
//             INNER JOIN dmirh_personal_time_detail as b
//             on a.id=b.dmirh_personal_time_id inner join dmirh_cat_time_status as c on a.dmirh_cat_time_status_id=c.id
//                               where a.user=max_user.user
//                               and a.created_at=max_user.creado and a.user='".$pers->usuario_ad."'");
//                               select * from dmirh_personal_time as a inner join dmirh_personal_time_detail as b
// on a.id= b.dmirh_personal_time_id inner join dmirh_cat_time_status as c on
// a.dmirh_cat_time_status_id=c.id where a.active=1

        //  $res2=  PersonalTime::where('user',$pers->usuario_ad)->where('active',1)->first();
            $res2= DB::table('dmirh_personal_time')
            ->join('dmirh_personal_time_detail', 'dmirh_personal_time.id', '=', 'dmirh_personal_time_detail.dmirh_personal_time_id')
            ->join('cat_time_status', 'dmirh_personal_time.dmirh_cat_time_status_id', '=', 'cat_time_status.id')
            ->where('dmirh_personal_time.active',1)->where('dmirh_personal_time.user',$pers->usuario_ad)->orderBy("dmirh_personal_time.id","desc")->get();
            
            $hours_week=0;
            $special_situation=0;

            foreach($res2 as $hor){

                 switch ($hor->week_day) {
                case 1:
                    array_push($lunes,(object)[
                        "entrada" =>$hor->entry_hour,
                        "comida1" =>$hor->exit_food_hour,
                        "comida2" =>$hor->entry_food_hour,
                        "salida" =>$hor->exit_hour,
                      ]);
                    break;
                case 2:
                    array_push($martes,(object)[
                        "entrada" =>$hor->entry_hour,
                        "comida1" =>$hor->exit_food_hour,
                        "comida2" =>$hor->entry_food_hour,
                        "salida" =>$hor->exit_hour,
                      ]);
                    break;
                case 3:
                    array_push($miercoles,(object)[
                        "entrada" =>$hor->entry_hour,
                        "comida1" =>$hor->exit_food_hour,
                        "comida2" =>$hor->entry_food_hour,
                        "salida" =>$hor->exit_hour,
                      ]);
                    break;
                case 4:
                    array_push($jueves,(object)[
                        "entrada" =>$hor->entry_hour,
                        "comida1" =>$hor->exit_food_hour,
                        "comida2" =>$hor->entry_food_hour,
                        "salida" =>$hor->exit_hour,
                      ]);
                    break;
                case 5:
                    array_push($viernes,(object)[
                        "entrada" =>$hor->entry_hour,
                        "comida1" =>$hor->exit_food_hour,
                        "comida2" =>$hor->entry_food_hour,
                        "salida" =>$hor->exit_hour,
                      ]);
                    break;
                case 6:
                    array_push($sabado,(object)[
                        "entrada" =>$hor->entry_hour,
                        "comida1" =>$hor->exit_food_hour,
                        "comida2" =>$hor->entry_food_hour,
                        "salida" =>$hor->exit_hour,
                      ]);
                    break;
                case 7:
                    array_push($domingo,(object)[
                        "entrada" =>$hor->entry_hour,
                        "comida1" =>$hor->exit_food_hour,
                        "comida2" =>$hor->entry_food_hour,
                        "salida" =>$hor->exit_hour,
                      ]);
                    break;
                }
                $status= $hor->description;
                $vigencia= Carbon::parse($hor->start_date)->format('d/m/Y');
                $hours_week += floatval($hor->hours_day);
                $special_situation = $hor->special_situation;

            }
            array_push($objHorarios,(object)[
              "personal_id" => $pers->personal_id,
              "personal_intelisis_usuario_ad" => $pers->usuario_ad,
              "name" => $pers->name." ".$pers->last_name,
              "lunes" =>$lunes,
              "martes" =>$martes,
              "miercoles" =>$miercoles,
              "jueves" =>$jueves,
              "viernes" =>$viernes,
              "sabado" =>$sabado,
              "domingo" =>$domingo,
              "status"=>  $status,
              "special_situation" => $special_situation,
              "vigencia" => $vigencia,
              "hours_week"=> $hours_week,
              "special_situation"=> $special_situation,
            ]);
        }

        return response()->json($objHorarios , 200);
    }

    protected function addHorarioPersonal(Request $request){

      // try {
        $datos= $this->validate(request(),[
          'user' => 'required',
          'vigencia' => 'required',
          'jornada' => 'required'
          ]);
          try {
            $upd= PersonalTime::where("active",1)->where("user",$datos["user"])->first();
            if($upd !=""){
            $usr= PersonalTime::find($upd->id);
            $usr->active=0;
            $usr->save();
            }

            if(isset($request->Idtime) && $request->Idtime > 0){
              $delete= PersonalTime::where("id",$request->Idtime)->where("user",$datos["user"])->first();
              $delete->deleted_at = Carbon::now();
              $delete->save();
            }
          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
          }

          $active_period = DmiControlProcedureValidation::where('key','ControlAsistencia_solicitar_cambio_horario-periodo-activo')->first();

          $nom_usuario=auth()->user()->usuario;
          $personaltime = new PersonalTime();
          $personaltime->user = $datos["user"];
          $personaltime->dmirh_cat_time_status_id = 1;
          $personaltime->start_date = $datos["vigencia"];
          $personaltime->approved_by = $nom_usuario;
          $personaltime->approved_date =Carbon::now();
          $personaltime->active =1;
          $personaltime->special_situation = $request["comentarios"];
          $personaltime->request_period = $active_period != null ? $active_period->value : Carbon::now()->format('Y-m-d');
          $personaltime->edit = 0;
          $personaltime->number_cancellations = 0;
          $personaltime->save();
          $personaltimeId=DB::getPdo()->lastInsertId();

          if($request["comentarios"] != ""){
            $personaltimecomment = new PersonalTimeComment();
            $personaltimecomment->dmirh_personal_time_id =  $personaltimeId;
            $personaltimecomment->comment=$request["comentarios"];
            $personaltimecomment->save();
          }

          $arrJornada= explode(",",$datos["jornada"]);

          foreach ( $arrJornada as $value) {
            $personaltimeDetail = new PersonalTimeDetail();
            $personaltimeDetail->dmirh_personal_time_id= $personaltimeId;

            if($value =="Lunes"){
            $personaltimeDetail->week_day= 1;
            $personaltimeDetail->entry_hour= $request["LunesEntrada"] === 'null' || $request["LunesEntrada"] == null || $request["LunesEntrada"] == "" ? '00:00' : $request["LunesEntrada"];
            $personaltimeDetail->exit_food_hour= $request["LunesComida1"] === 'null' || $request["LunesComida1"] == null || $request["LunesComida1"] == "" ? '00:00' : $request["LunesComida1"];
            $personaltimeDetail->entry_food_hour= $request["LunesComida2"] === 'null' || $request["LunesComida2"] == null || $request["LunesComida2"] == "" ? '00:00' : $request["LunesComida2"];
            $personaltimeDetail->exit_hour= $request["LunesSalida"] === 'null' || $request["LunesSalida"] == null || $request["LunesSalida"] == "" ? '00:00' : $request["LunesSalida"];
            }else if($value =="Martes"){
              $personaltimeDetail->week_day= 2;
              $personaltimeDetail->entry_hour= $request["MartesEntrada"] === 'null' || $request["MartesEntrada"] == null || $request["MartesEntrada"] == "" ? '00:00' : $request["MartesEntrada"];
              $personaltimeDetail->exit_food_hour= $request["MartesComida1"] === 'null' || $request["MartesComida1"] == null || $request["MartesComida1"] == "" ? '00:00' : $request["MartesComida1"];
              $personaltimeDetail->entry_food_hour= $request["MartesComida2"] === 'null' || $request["MartesComida2"] == null || $request["MartesComida2"] == "" ? '00:00' : $request["MartesComida2"];
              $personaltimeDetail->exit_hour= $request["MartesSalida"] === 'null' || $request["MartesSalida"] == null || $request["MartesSalida"] == "" ? '00:00' : $request["MartesSalida"];
            }else if($value =="Miercoles"){
              $personaltimeDetail->week_day= 3;
              $personaltimeDetail->entry_hour= $request["MiercolesEntrada"] === 'null' || $request["MiercolesEntrada"] == null || $request["MiercolesEntrada"] == "" ? '00:00' : $request["MiercolesEntrada"];
              $personaltimeDetail->exit_food_hour= $request["MiercolesComida1"] === 'null' || $request["MiercolesComida1"] == null || $request["MiercolesComida1"] == "" ? '00:00' : $request["MiercolesComida1"];
              $personaltimeDetail->entry_food_hour= $request["MiercolesComida2"] === 'null' || $request["MiercolesComida2"] == null || $request["MiercolesComida2"] == "" ? '00:00' : $request["MiercolesComida2"];
              $personaltimeDetail->exit_hour= $request["MiercolesSalida"] === 'null' || $request["MiercolesSalida"] == null || $request["MiercolesSalida"] == "" ? '00:00' : $request["MiercolesSalida"];
            }else if($value =="Jueves"){
              $personaltimeDetail->week_day= 4;
              $personaltimeDetail->entry_hour= $request["JuevesEntrada"] === 'null' || $request["JuevesEntrada"] == null || $request["JuevesEntrada"] == "" ? '00:00' : $request["JuevesEntrada"];
              $personaltimeDetail->exit_food_hour= $request["JuevesComida1"] === 'null' || $request["JuevesComida1"] == null || $request["JuevesComida1"] == "" ? '00:00' : $request["JuevesComida1"];
              $personaltimeDetail->entry_food_hour= $request["JuevesComida2"] === 'null' || $request["JuevesComida2"] == null || $request["JuevesComida2"] == "" ? '00:00' : $request["JuevesComida2"];
              $personaltimeDetail->exit_hour= $request["JuevesSalida"] === 'null' || $request["JuevesSalida"] == null || $request["JuevesSalida"] == "" ? '00:00' : $request["JuevesSalida"];

            }else if($value =="Viernes"){
              $personaltimeDetail->week_day= 5;
              $personaltimeDetail->entry_hour= $request["ViernesEntrada"] === 'null' || $request["ViernesEntrada"] == null || $request["ViernesEntrada"] == "" ? '00:00' : $request["ViernesEntrada"];
              $personaltimeDetail->exit_food_hour= $request["ViernesComida1"] === 'null' || $request["ViernesComida1"] == null || $request["ViernesComida1"] == "" ? '00:00' : $request["ViernesComida1"];
              $personaltimeDetail->entry_food_hour= $request["ViernesComida2"] === 'null' || $request["ViernesComida2"] == null || $request["ViernesComida2"] == "" ? '00:00' : $request["ViernesComida2"];
              $personaltimeDetail->exit_hour= $request["ViernesSalida"] === 'null' || $request["ViernesSalida"] == null || $request["ViernesSalida"] == "" ? '00:00' : $request["ViernesSalida"];
            }else if($value =="Sabado"){
              $personaltimeDetail->week_day= 6;
              $personaltimeDetail->entry_hour= $request["SabadoEntrada"] === 'null' || $request["SabadoEntrada"] == null || $request["SabadoEntrada"] == "" ? '00:00' : $request["SabadoEntrada"];
              $personaltimeDetail->exit_food_hour= $request["SabadoComida1"] === 'null' || $request["SabadoComida1"] == null || $request["SabadoComida1"] == "" ? '00:00' : $request["SabadoComida1"];
              $personaltimeDetail->entry_food_hour= $request["SabadoComida2"] === 'null' || $request["SabadoComida2"] == null || $request["SabadoComida2"] == "" ? '00:00' : $request["SabadoComida2"];
              $personaltimeDetail->exit_hour= $request["SabadoSalida"] === 'null' || $request["SabadoSalida"] == null || $request["SabadoSalida"] == "" ? '00:00' : $request["SabadoSalida"];
            }else if($value =="Domingo"){
              $personaltimeDetail->week_day= 7;
              $personaltimeDetail->entry_hour= $request["DomingoEntrada"] === 'null' || $request["DomingoEntrada"] == null || $request["DomingoEntrada"] == "" ? '00:00' : $request["DomingoEntrada"];
              $personaltimeDetail->exit_food_hour= $request["DomingoComida1"] === 'null' || $request["DomingoComida1"] == null || $request["DomingoComida1"] == "" ? '00:00' : $request["DomingoComida1"];
              $personaltimeDetail->entry_food_hour= $request["DomingoComida2"] === 'null' || $request["DomingoComida2"] == null || $request["DomingoComida2"] == "" ? '00:00' : $request["DomingoComida2"];
              $personaltimeDetail->exit_hour= $request["DomingoSalida"] === 'null' || $request["DomingoSalida"] == null || $request["DomingoSalida"] == "" ? '00:00' : $request["DomingoSalida"];
            }

            // Se calcula las horas que se trabajaran en ese día y con ese horario asignado
            try {

              $entry_work = Carbon::createFromFormat('H:i:s', $personaltimeDetail->entry_hour);
              $exit_lunchtime = Carbon::createFromFormat('H:i:s', $personaltimeDetail->exit_food_hour);
              $first_hours = $exit_lunchtime->diffInHours($entry_work);

              $entry_lunchtime = Carbon::createFromFormat('H:i:s', $personaltimeDetail->entry_food_hour);
              $exit_work = Carbon::createFromFormat('H:i:s', $personaltimeDetail->exit_hour);
              $last_hours = $exit_work->diffInHours($entry_lunchtime);

              $personaltimeDetail->hours_day= floatval($first_hours) + floatval($last_hours);
            } catch (\Throwable $th) {
              $personaltimeDetail->hours_day = 0;
            }

            

            $personaltimeDetail->save();
          }

          $collaborator = PersonalIntelisis::where('usuario_ad',$personaltime->user)
                                          ->where('status','ALTA')->first();

          $status = CatTimeStatus::find($personaltime->dmirh_cat_time_status_id);

          $this->sendEmail->ChangeWorkScheduleNotification([
              'data' =>[
                  'rrhh_full_name'=> auth()->user()->personal_intelisis->name.' '.auth()->user()->personal_intelisis->last_name,
                  'collaborator_full_name' => $collaborator->name.' '.$collaborator->last_name,
                  'type' => "collaborator",
                  'status' => $status->description,
                  'start_date' => Carbon::parse(date('Y-m-d H:i:s'))->format('d-m-Y'),
              ],    
              'to_email' =>$collaborator->email,
              'module' =>'control_horarios',
          ]);

          return response()->json(['success'=>'Se guardo exitosamente.'],200);

        // return response()->json(['error' => 'Error al guardar'], 500);

      // } catch (\Throwable $th) {
      //   return response()->json(['error' => $th], 500);
      // }


    }

    protected function getCatTimeStatus(){
      if(Auth::check()){
      $res= CatTimeStatus::all();

        return response()->json($res, 200);
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }

    }

    protected function addCatTimeStatus(){
      $datos= $this->validate(request(),[

        'description' => 'required',
        ]);

      if(Auth::check()){

          try {
            $status= new CatTimeStatus();

         $status->description= $datos["description"];
         $status->save();

          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al insertar.'],500);
          }


         return response()->json(['success'=>' Status agregado Correctamente.'],200);
  }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }

    }


    protected function updateCatTimeStatus(){
      $datos= $this->validate(request(),[

        'id' => 'required',
        'description' => 'required',
        'deleted' => 'required',
        ]);

      if(Auth::check()){

        try {
            $status= CatTimeStatus::find($datos["id"]);

          $status->description= $datos["description"];
          $status->deleted= $datos["deleted"];
          $status->save();

          } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
          }


         return response()->json(['success'=>'Status Modificado Correctamente.'],200);
    }else{

      return response()->json(['error'=>'No tienes Sesion'],200);
      }

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
      // $res = DB::select("Select * from dmirh_work_schedules where type='Comida'");

      return response()->json($res, 200);
    }else{

      return response()->json(['error'=>'No tienes Sesion'],200);
      }
  }

}
