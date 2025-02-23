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
use App\Services\SendEmailService;

class PanelHorariosAprobarController extends Controller
{

  private $sendEmail;

	public function __construct(SendEmailService $sendEmail)
	{
        //$this->middleware('guest',['only'=>'ShowLogin']);
		$this->sendEmail = $sendEmail;
	}

  protected function getHorariosPersonalAprobar(Request $request){

    $datos= $this->validate(request(),[

      'location' => 'required'

      ]);
    $objHorarios=[];

      $arrMipersonal= DB::table('dmirh_personal_time')
      ->join('personal_intelisis', 'dmirh_personal_time.user', '=', 'personal_intelisis.usuario_ad')
      ->join('cat_time_status', 'dmirh_personal_time.dmirh_cat_time_status_id', '=', 'cat_time_status.id')
      ->whereNull('dmirh_personal_time.deleted_at')
      ->whereNull('cat_time_status.deleted_at')
      ->where('cat_time_status.description','Autorizado')->where('personal_intelisis.status','ALTA')->where('personal_intelisis.location',$datos["location"])->get();

    foreach($arrMipersonal as $pers){
      $lunes=array();
      $martes=array();
      $miercoles=array();
      $jueves=array();
      $viernes=array();
      $sabado=array();
      $domingo=array();
      $status="";
      $idpersonalTime="";
      $approved="";
        // $res2= DB::select("select * from dmirh_personal_time as a
        // INNER JOIN dmirh_personal_time_detail as b
        // on a.id=b.dmirh_personal_time_id inner join dmirh_cat_time_status as c on a.dmirh_cat_time_status_id=c.id
        //                   where c.description='Autorizado' and
        //               b.deleted=0 and a.user='". $pers->usuario_ad."'");
                      $res2= PersonalTime::join('dmirh_personal_time_detail', 'dmirh_personal_time.id', '=', 'dmirh_personal_time_detail.dmirh_personal_time_id')
                      ->join('cat_time_status', 'dmirh_personal_time.dmirh_cat_time_status_id', '=', 'cat_time_status.id')
                      ->whereNull('dmirh_personal_time_detail.deleted_at')
                      ->whereNull('cat_time_status.deleted_at')
                      ->where('cat_time_status.description','Autorizado')->where('dmirh_personal_time.user',$pers->usuario_ad)->get();

        if(count($res2) > 0){

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
            $idpersonalTime= $hor->dmirh_personal_time_id;
            $approved= $hor->approved_by;
            $hours_week= $hor->hours_week;
            $special_situation= $hor->special_situation;

        }-

        array_push($objHorarios,(object)[
           "personal_id" => $pers->personal_id,
           "personal_intelisis_usuario_ad" => $pers->usuario_ad,
           "name" => $pers->name,
           "last_name" => $pers->last_name,
           "lunes" =>$lunes,
           "martes" =>$martes,
           "miercoles" =>$miercoles,
           "jueves" =>$jueves,
           "viernes" =>$viernes,
           "sabado" =>$sabado,
           "domingo" =>$domingo,
           "status"=>  $status,
           "Idtime"=>  $idpersonalTime,
           "approved_by"=> $approved,
           "hours_week"=> $hours_week,
           "special_situation"=> $special_situation,
           
          ]);
    }
  }


    return response()->json($objHorarios , 200);
}

  protected function autorizarHorarioPersonal(){

    $datos= $this->validate(request(),[

      'IdTime' => 'required'

      ]);
      try {
        $personalHorario= PersonalTime::find($datos["IdTime"]);
        $upd= PersonalTime::where("active",1)->where("user",$personalHorario->user)->where("dmirh_cat_time_status_id",1)->get();
        if(count($upd)>0){
          foreach($upd as $row) {
        $usr= PersonalTime::find($row->id);
        $usr->active=0;
        $usr->save();
        }
      }
      } catch (\Throwable $th) {
        return response()->json(['error'=>'Error al modificar.'],500);
      }
      $usuario =auth()->user()->usuario;
      $personalHorario= PersonalTime::find($datos["IdTime"]);
      $personalHorario->dmirh_cat_time_status_id= 1;
      $personalHorario->start_date= date('Y-m-d H:i:s');
      $personalHorario->approved_by= $usuario;
      $personalHorario->active= 1;
      $personalHorario->approved_date= date('Y-m-d H:i:s');
      $personalHorario->save();

      $collaborator = PersonalIntelisis::where('usuario_ad',$personalHorario->user)
                                      ->where('status','ALTA')->first();

      $status = CatTimeStatus::find($personalHorario->dmirh_cat_time_status_id);

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

    return response()->json(['success'=>'Horario Aprobado Correctamente.'],200);
  }

  protected function rechazarHorarioPersonal(Request $request){

    $datos= $this->validate(request(),[
      'IdTime' => 'required'
    ]);

    $personalHorario= PersonalTime::find($datos["IdTime"]);
    $personalHorario->dmirh_cat_time_status_id= 5;
    $personalHorario->number_cancellations= intval($personalHorario->number_cancellations)+1;
    $personalHorario->deleted= 1;
    $personalHorario->save();

    $collaborator = PersonalIntelisis::where('usuario_ad',$personalHorario->user)
                                      ->where('status','ALTA')->first();

    $status = CatTimeStatus::find($personalHorario->dmirh_cat_time_status_id);

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

    return response()->json(['success'=>'Horario Cancelado Correctamente.'],200);
  }
}
