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
use App\Services\SendEmailService;
use App\Repositories\GeneralFunctionsRepository;

class AutorizarHorariosAreaController extends Controller
{

  private $sendEmail, $GeneralFunctionsRepository;

	public function __construct(SendEmailService $sendEmail, GeneralFunctionsRepository $GeneralFunctionsRepository)
	{
        //$this->middleware('guest',['only'=>'ShowLogin']);
		$this->sendEmail = $sendEmail;
    $this->GeneralFunctionsRepository = $GeneralFunctionsRepository;
	}

  protected function getHorariosMiPersonalAutorizar(){
    $usuario =auth()->user()->usuario;
    // $plaza= DB::select("select plaza_id,top_plaza_id from personal_intelisis where usuario_ad='arturo.jara' and status='ALTA'");
    // $plaza= DB::select("select plaza_id,top_plaza_id from personal_intelisis where usuario_ad='".$usuario."' and status='ALTA'");
    $plaza= PersonalIntelisis::where("usuario_ad",$usuario)->where("status","ALTA")->select("plaza_id","top_plaza_id")->first();

    $objHorarios=[];
    // $arrMipersonal= DB::select("select * from personal_intelisis where top_plaza_id='".$plaza[0]->plaza_id."' and status='ALTA'");
    $arrMipersonal= PersonalIntelisis::where("top_plaza_id",$plaza->plaza_id)->where("status","ALTA")->get();

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
        // $res2= DB::select("
        // select * from dmirh_personal_time as a
        // INNER JOIN (select user,max(id) as creado
        //                              from dmirh_personal_time where dmirh_cat_time_status_id=2
        //                              group by user) max_user
        //         INNER JOIN dmirh_personal_time_detail as b
        //         on a.id=b.dmirh_personal_time_id inner join dmirh_cat_time_status as c on a.dmirh_cat_time_status_id=c.id
        //                           where c.description='Pendiente' and
        //                           a.user='". $pers->usuario_ad."'  and b.deleted=0 and a.id=max_user.creado");
    $res2= PersonalTime::join('dmirh_personal_time_detail', 'dmirh_personal_time.id', '=', 'dmirh_personal_time_detail.dmirh_personal_time_id')
    ->join('cat_time_status', 'dmirh_personal_time.dmirh_cat_time_status_id', '=', 'cat_time_status.id')
    ->where('cat_time_status.description','Pendiente')->where('dmirh_personal_time.active',1)->where('dmirh_personal_time.user',$pers->usuario_ad)->get();
    
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
            $hours_week= $hor->hours_week;
            $special_situation= $hor->special_situation;

        }

        array_push($objHorarios,(object)[
           "personal_id" => $pers->personal_id,
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
           "hours_week"=>  $hours_week,
           "special_situation"=>  $special_situation,
        ]);
    }
  }


    return response()->json($objHorarios , 200);
}

protected function autorizarHorarioPersonal(Request $request){

    $datos= $this->validate(request(),[
    'IdTime' => 'required'
    ]);

    $personalHorario= PersonalTime::find($datos["IdTime"]);
    $personalHorario->dmirh_cat_time_status_id= 4;
    $personalHorario->approved_by= auth()->user()->usuario;
    $personalHorario->approved_date= date('Y-m-d H:i:s');
    $personalHorario->save();

    $collaborator = PersonalIntelisis::where('usuario_ad',$personalHorario->user)
                                    ->where('status','ALTA')->first();

    $status = CatTimeStatus::find($personalHorario->dmirh_cat_time_status_id);

    $this->sendEmail->ChangeWorkScheduleNotification([
        'data' =>[
            'boss_full_name'=> auth()->user()->personal_intelisis->name.' '.auth()->user()->personal_intelisis->last_name,
            'collaborator_full_name' => $collaborator->name.' '.$collaborator->last_name,
            'type' => "collaborator",
            'status' => $status->description,
        ],    
        'to_email' =>$collaborator->email,
        'module' =>'control_horarios',
    ]);

    $coordinadora_rrhh = $this->GeneralFunctionsRepository->getCoordinadoraRRHH($collaborator->location);
    if($coordinadora_rrhh['success'] == 1){
        $this->sendEmail->ChangeWorkScheduleNotification([
            'data' =>[
                'boss_full_name'=> auth()->user()->personal_intelisis->name.' '.auth()->user()->personal_intelisis->last_name,
                'collaborator_full_name' => $collaborator->name.' '.$collaborator->last_name,
                'rrhh_full_name' => $coordinadora_rrhh["data"]['name'].' '.$coordinadora_rrhh["data"]['last_name'],
                'type' => "rrhh",
                'status' => $status->description,
            ],    
            'to_email' =>$coordinadora_rrhh["data"]['email'],
            'module' =>'control_horarios',
        ]);
    }


    return response()->json(['success'=>'Horario Autorizado Correctamente.'],200);
}
protected function rechazarHorarioPersonal(Request $request){

  $datos= $this->validate(request(),[

    'IdTime' => 'required'

    ]);

    $personalHorario= PersonalTime::find($datos["IdTime"]);
    $personalHorario->dmirh_cat_time_status_id= 3;
    $personalHorario->approved_by= auth()->user()->usuario;
    $personalHorario->number_cancellations= intval($personalHorario->number_cancellations)+1;
    if($personalHorario->number_cancellations >= 2){
      $personalHorario->edit = 0;
    }else{
      $personalHorario->edit = 1;
    }
    
    $personalHorario->save();

    $collaborator = PersonalIntelisis::where('usuario_ad',$personalHorario->user)
                                      ->where('status','ALTA')->first();
    $status = CatTimeStatus::find($personalHorario->dmirh_cat_time_status_id);

    $this->sendEmail->ChangeWorkScheduleNotification([
      'data' =>[
          'boss_full_name'=> auth()->user()->personal_intelisis->name.' '.auth()->user()->personal_intelisis->last_name,
          'collaborator_full_name' => $collaborator->name.' '.$collaborator->last_name,
          'type' => "collaborator",
          'status' => $status->description,
      ],    
      'to_email' =>$collaborator->email,
      'module' =>'control_horarios',
  ]);

  return response()->json(['success'=>'Horario rechazado Correctamente.'],200);
}
}
