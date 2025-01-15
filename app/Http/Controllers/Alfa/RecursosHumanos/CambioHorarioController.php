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
use App\Models\PersonalIntelisis;
use App\Models\DmiControlProcedureValidation;
use App\Services\SendEmailService;

class CambioHorarioController extends Controller
{

    private $sendEmail;

	public function __construct(SendEmailService $sendEmail)
	{
        //$this->middleware('guest',['only'=>'ShowLogin']);
		$this->sendEmail = $sendEmail;
	}



    /* protected function addCambioHorarioPersonal(Request $request){

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


    } */
    protected function addCambioHorarioPersonal(Request $request){
        $usuario=auth()->user()->usuario;

        $datos= $this->validate(request(),[
				'selected_day' => 'required',
				'days_week' => 'required',
				]);

		
        $upd= PersonalTime::where("active",1)->where("user",$usuario)->where("dmirh_cat_time_status_id",2)->first();
        if($upd !=""){
            $usr= PersonalTime::find($upd->id);
            $usr->active=0;
            $usr->save();
        }

        $active_period = DmiControlProcedureValidation::where('key','ControlAsistencia_solicitar_cambio_horario-periodo-activo')->first();

		$personal_time = new PersonalTime();
		$personal_time->user = $usuario;
		$personal_time->dmirh_cat_time_status_id = 2;
		$personal_time->active = 1;
		$personal_time->special_situation = $request->special_situation;
		$personal_time->request_period = $active_period != null ? $active_period->value : Carbon::now()->format('Y-m-d');
        $personal_time->edit = 0;
		$personal_time->save();

		if($personal_time != null){
			$work_schedule = $request->days_week;

			foreach($work_schedule as $day){
				if($day['work_schedule'] != null){
					$personaltimeDetail = new PersonalTimeDetail();
                    $personaltimeDetail->dmirh_personal_time_id= $personal_time->id;
					$personaltimeDetail->week_day= $day['day_id'];
					$personaltimeDetail->entry_hour= $day['work_schedule']['entry_work'];
					$personaltimeDetail->exit_food_hour= $day['work_schedule']['exit_lunchtime'];
					$personaltimeDetail->entry_food_hour= $day['work_schedule']['entry_lunchtime'];
					$personaltimeDetail->exit_hour= $day['work_schedule']['exit_work'];
					$personaltimeDetail->hours_day= $day['hours_day'];
                    $personaltimeDetail->save();
				}
			}

		}

        $boss = PersonalIntelisis::where('plaza_id',auth()->user()->personal_intelisis->top_plaza_id)
                                    ->where('status','ALTA')->first();
        $this->sendEmail->ChangeWorkScheduleNotification([
            'data' =>[
                'collaborator_full_name'=> auth()->user()->personal_intelisis->name.' '.auth()->user()->personal_intelisis->last_name,
                'boss_full_name' => $boss->name.' '.$boss->last_name,
                'type' => "boss",
            ],    
            'to_email' =>$boss->email,
            'module' =>'control_horarios',
        ]);

        return response()->json(['success'=>1,'data'=>$personal_time,'message'=>"Se guardo correctamente."],200);
    }

    /* protected function updateCambioHorarioPersonal(Request $request){

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


    } */
    protected function updateCambioHorarioPersonal(Request $request){

      // try {
        $datos= $this->validate(request(),[
                                        'personal_time_id' => 'required',
                                        'special_situation' => 'required',
                                        'days_week' => 'required',
                                    ]);

        $personal_time_update = PersonalTime::find($request->personal_time_id);
        $personal_time_update->special_situation = $request->special_situation;
        $personal_time_update->dmirh_cat_time_status_id = 2;
        $personal_time_update->edit = 0;
        $personal_time_update->save();

        //Inicia el proceso para editar los items
        foreach ($request->days_week as $key => $day) {
            if($day['action'] == 'new' && $day['work_schedule'] != null){
                $personaltimeDetail = new PersonalTimeDetail();
                $personaltimeDetail->dmirh_personal_time_id= $personal_time_update->id;
                $personaltimeDetail->week_day= $day['day_id'];
                $personaltimeDetail->entry_hour= $day['work_schedule']['entry_work'];
                $personaltimeDetail->exit_food_hour= $day['work_schedule']['exit_lunchtime'];
                $personaltimeDetail->entry_food_hour= $day['work_schedule']['entry_lunchtime'];
                $personaltimeDetail->exit_hour= $day['work_schedule']['exit_work'];
                $personaltimeDetail->hours_day= $day['hours_day'];
                $personaltimeDetail->save();

            }else if($day['action'] == 'update' && $day['personal_time_details_id'] > 0){
                $personaltimeDetail = PersonalTimeDetail::find($day['personal_time_details_id']);

                if($personaltimeDetail != null){
                    $personaltimeDetail->entry_hour= $day['work_schedule']['entry_work'];
                    $personaltimeDetail->exit_food_hour= $day['work_schedule']['exit_lunchtime'];
                    $personaltimeDetail->entry_food_hour= $day['work_schedule']['entry_lunchtime'];
                    $personaltimeDetail->exit_hour= $day['work_schedule']['exit_work'];
                    $personaltimeDetail->save();
                }
                
            }else if($day['action'] == 'delete' && $day['personal_time_details_id'] > 0){
                $personaltimeDetail = PersonalTimeDetail::where('id',$day['personal_time_details_id'])->first();
                $personaltimeDetail->deleted = 1;
                $personaltimeDetail->deleted_at = Carbon::now();
                $personaltimeDetail->save();
                //$personaltimeDetail = PersonalTimeDetail::where('id',$day['personal_time_details_id'])->delete();
            }
        }

        $personal_time_update = PersonalTime::find($request->personal_time_id);

        return response()->json(['success'=> 1,"data"=>$personal_time_update,"message"=>'Se actualizo exitosamente.'],200);


    }

    protected function getHourEntrance(){
      if(Auth::check()){
        $res = WorkSchedule::where("type","Entrada")->orderBy('hour','asc')->get();

        return response()->json($res, 200);
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }
    protected function getHourFood(){
      if(Auth::check()){
      $res = WorkSchedule::where("type","SalidaComida")->orderBy('hour','asc')->get();

      return response()->json($res, 200);
    }else{

      return response()->json(['error'=>'No tienes Sesion'],200);
      }
    }

    protected function getHourEntryFood(){
      if(Auth::check()){
      $res = WorkSchedule::where("type","EntradaComida")->orderBy('hour','asc')->get();

      return response()->json($res, 200);
    }else{

      return response()->json(['error'=>'No tienes Sesion'],200);
      }
    }

    protected function getHorarioPendiente(){
        if(Auth::check()){
            $usuario =auth()->user()->usuario;

            /* $res= DB::table('dmirh_personal_time')
            ->join('dmirh_personal_time_detail', 'dmirh_personal_time.id', '=', 'dmirh_personal_time_detail.dmirh_personal_time_id')
            ->join('cat_time_status', 'dmirh_personal_time.dmirh_cat_time_status_id', '=', 'cat_time_status.id')
            ->where('dmirh_personal_time.active',1)->where('dmirh_personal_time.user',$usuario)
            ->where(function ($query) {
                                        $query->where('cat_time_status.description', '=', 'Pendiente')
                                                ->orWhere('cat_time_status.description', '=', 'Autorizado');
                                    })->get(); */

            $personal_time = PersonalTime::with(['dmirh_personal_time_details','dmirh_cat_time_status'])
                                            ->where('dmirh_personal_time.user',auth()->user()->usuario)
                                            ->where('dmirh_personal_time.active',1)
                                            ->whereNull('dmirh_personal_time.deleted_at')
                                            ->whereRelation('dmirh_cat_time_status',function($q){
                                                return $q->where('description','Pendiente')
                                                        ->orWhere('description','Rechazado');
                                            })
                                            ->first();

            return response()->json(["success"=> 1,'data'=>$personal_time], 200);
        }else{

            return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }

    protected function cancelCambioHorarioPersonal(Request $request){
        if(Auth::check()){
            $usuario =auth()->user()->usuario;
            $personal_time = PersonalTime::where('id',$request->personal_time_id)->first();
            $personal_time->dmirh_cat_time_status_id = 5;
            $personal_time->save();

            return response()->json(["success"=> 1], 200);
        }else{

            return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }

    public function validateRejectedQuantity(){

        $sum_number_cancellations=0;

        // Periodo actual activo
        $active_period = DmiControlProcedureValidation::where('key','ControlAsistencia_solicitar_cambio_horario-periodo-activo')->first();
        $requests = PersonalTime::where('user',auth()->user()->usuario)
                                ->where('deleted',0)
                                ->where('request_period',$active_period->value)
                                ->get();

        if(sizeof($requests) > 0){
            foreach ($requests as $key => $request) {
                $sum_number_cancellations += intval($request->number_cancellations);
            }
        }else{
            $requests = PersonalTime::where('user',auth()->user()->usuario)
                                ->where('dmirh_cat_time_status_id',5)
                                ->where('deleted',1)
                                ->where('request_period',$active_period->value)
                                ->get();
            if(sizeof($requests) > 0){
                $sum_number_cancellations = 2;
            }
        }

        return ['success' => 1,'number_cancellations'=>$sum_number_cancellations];

    }

}
