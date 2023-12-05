<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis as PersonalIntelisis;
use App\Models\DmiRh\DmirhPersonalTime as PersonalTime;
use App\Models\DmiRh\DmirhAttendancePolicy as AttendancePolicy;
use App\Models\DmiRh\DmirhPersonalTimeDetail as PersonalTimeDetail;
use App\Models\DmiRh\DmirhPersonalTimeComment as PersonalTimeComment;
use App\Models\DmiRh\DmirhPersonalJustification as PersonalJustification;
use App\Models\DmiRh\DmirhWorkPermit as WorkPermit;
use App\Models\DmiRh\DmirhVacation as Vacation;
use App\Models\DmiRh\DmirhCatTimeStatus as CatTimeStatus;
use App\Models\CatDaysOff;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class ReporteAsistenciaController extends Controller
{
    protected function getReporteAsistencia(Request $request){

        $datos= $this->validate(request(),[
        
            'fechas' => 'required', 
            'ubications' => 'required', 
            ]);
            $objReporte=[];
            $arrayUbicacion= explode(",",$datos["ubications"]);
            $arrayFechas= explode(",",$datos["fechas"]);
            $inicio=$arrayFechas[0];
            $fin=$arrayFechas[1];
            $tol="";
            $punt="";
            $retardo="";
            $susp="";
            try {
                //code...
          
    foreach ( $arrayUbicacion as $value) {
        $personal = PersonalIntelisis::where('location',$value)->where('status','ALTA')->orderby('name')->get();
        $policy = AttendancePolicy::where('location',$value)->first();

                $tol= '+'.$policy->tolerance.' minute';
                $punt= '+'.$policy->puntuality.' minute';
                $susp= '+'.$policy->suspension.' minute';
                $retardo= '+'.$policy->delay.' minute';

        foreach ( $personal as $pers) {
            if($inicio> $fin){
                $inicio=$arrayFechas[0];
            }
            $arrayCheck= $this->Fecha_Checada_All($inicio,$fin,$pers->previous_personal_id,$pers->rfc);
            // dd($arrayChec);
            // exit;
            // return response()->json($arrayChec,200);
            while($inicio <= $fin){
             $array=[];
             $arrayChec=[];
             $c1="";
             $c2="";
             $c3="";
             $c4="";
             $c5="";
             $c6="";
             $c7="";
               foreach ($arrayCheck as $key => $value) {
                    if($inicio == $value["fecha"]){
                        $array= explode(".",$value[1]);
                       array_push($arrayChec,$array[0]);
                    }
               }
               sort($arrayChec);
               foreach ($arrayChec as $key => $value) {
               switch ($key) {
                case 0:
                 $c1=$value;
                    break;
                case 1:
                    $c2=$value;
                    break;
                case 2:
                    $c3=$value;
                    break;
                case 3:
                    $c4=$value;
                    break;
                case 4:
                    $c5=$value;
                    break;
                case 5:
                    $c6=$value;
                    break;
                case 6:
                    $c7=$value;
                    break;
               }
            }

            //    return response()->json($arrayCheck,200);
            // exit;
                // $arrayChec= $this->Fecha_Checada($inicio,$pers->previous_personal_id);
                $dia_fecha = date("N",strtotime($inicio));
                $nDia= $dia_fecha;
                $stjornada="";
                $hora_comida1="--";
                $hora_comida2="--";
                $hora_entrada="--";
                $hora_salida="--";
                $time_office="";
                $document="";
                $difWork="";
                $time_comidas="00:00:00";
                $time_trabajado="00:00:00";
                $trabajado="08:00:00";
                $full_trabajo="00:00:00";
                $extra="";
                $estatus="Suspensión";
                $vspuntualidad="";
                $h_ent="";
                $msg="";
                $fulljorn="";
                $exc="";
                //Obtenemos la informacion de horario  del empleado
                // $personal_time= PersonalTime::where('user',$pers->usuario_ad)->where('active',1)->first();
                $personal_time= PersonalTime::where('user',$pers->usuario_ad)->where('dmirh_cat_time_status_id',1)->where("start_date","<=",$inicio)
                ->where(function ($query) {
                    $query->where('active', '=', 1)
                          ->orWhere('active', '=', 0);
                })->orderBy("start_date","desc")->first();

                if( $personal_time!= ""){
                //obtenemos el detalle dia especifico del horario
                $time_detail= PersonalTimeDetail::where('dmirh_personal_time_id',$personal_time->id)->where('week_day',$dia_fecha)->first();

                if($time_detail != ""){
                    //Asignacion hora y salida de empleado
                $daysoff= CatDaysOff::where("date",$inicio)->first();

                // if($daysoff!= null){
                //     $entry_hour = Carbon::parse("08:00:00")->format('H:i');
                //     $exit_hour = Carbon::parse("15:00:00")->format('H:i');
                //     $exit_food = "";
                //     $entry_food = "";
                //     $exit_food_tol=date('H:i:s',strtotime("-60 minute", strtotime($exit_food)));
                //     $entry_food_tol=date('H:i:s',strtotime("+60 minute", strtotime($entry_food)));
                // }else{
                    if($daysoff!=null){
                        $exc="Viernes de Puente\n";
                    }

                    $entry_hour = Carbon::parse($time_detail->entry_hour)->format('H:i');
                    $exit_hour = Carbon::parse($time_detail->exit_hour)->format('H:i');
                    $exit_food = Carbon::parse($time_detail->exit_food_hour)->format('H:i');
                    $entry_food = Carbon::parse($time_detail->entry_food_hour)->format('H:i');
                    $exit_food_tol=date('H:i:s',strtotime("-60 minute", strtotime($exit_food)));
                    $entry_food_tol=date('H:i:s',strtotime("+60 minute", strtotime($entry_food)));
                // }
             
                // $exit_food_tole=new DateTime($exit_food_tol);
             //validamos la checadas en attendance    
                if($arrayChec){
                    if(count($arrayChec)==1){
                        $fechahora_entrada= current($arrayChec);
                        $fechahora_salida=  date("H:i:s",strtotime("00:00:00"));
                    }else{
                        $fechahora_entrada= current($arrayChec);

                        $fechahora_salida= end($arrayChec);

                    }
                    $hora_entrada=$fechahora_entrada;
                    $hora_comida1=date("H:i:s",strtotime("00:00:00"));
                    $hora_comida2=date("H:i:s",strtotime("00:00:00"));
                    $hora_salida=$fechahora_salida;
                    $entry_tol=date('H:i:s',strtotime("+60 minute", strtotime($hora_entrada)));
                    $exit_tol=date('H:i:s',strtotime("-30 minute", strtotime($hora_salida)));
                    $arrFoods=[];
                    // foreach ($arrayChec as $key => $hour) {
                    //     if($hour>=  $exit_food_tol && $hour <= $entry_food_tol){
                    //         array_push($arrFoods,$hour);
                    //     }
                    // }
                    if(count($arrayChec)>=4){
                        $msg="Registros Completos";
                    }else{
                        $msg="Registros Incompletos";

                    }
                 
                    if($fechahora_salida<$exit_hour ){
                        $msg.=", Salida Anticipada";
                    }
                  
                    foreach ($arrayChec as $key => $hour) {
                        if($hour>=  $entry_tol && $hour <= $exit_tol){
                            array_push($arrFoods,$hour);
                        }
                    }
                    if($arrFoods != null){
                        if(count($arrFoods)>1){
                        $hora_comida1=current($arrFoods);
                        $hora_comida2= end($arrFoods);
                        }else{
                            $hora_comida1=current($arrFoods);
                            $hora_comida2=date("H:i:s",strtotime("00:00:00"));
                        }
                    }
               
 
                    $h_ent= new DateTime($hora_entrada);
                    $h_sal= new DateTime($hora_salida);
                    
                    $f1 = new DateTime($hora_comida1);
                    $f2 = new DateTime($hora_comida2);
 
                    if($hora_comida1 !="00:00:00" && $hora_comida2 !="00:00:00" ){
                        $time_comids= $f1->diff($f2);
                        $time_comidas= $time_comids->format('%H:%I:%S');
                    }
                   
                    if($hora_entrada !="00:00:00" && $hora_salida !="00:00:00" ){
                        $hora_trabajo=$h_ent->diff($h_sal);
                        $full_trabajo=$hora_trabajo->format('%H:%I:%S');
                    }
                   
                        $full=  new DateTime($full_trabajo);
                        $com=  new DateTime($time_comidas);
                        $entry=new DateTime($entry_hour);
                        $food1=new DateTime($exit_food);
                        $food2=new DateTime($entry_food);
                        $exit=new DateTime($exit_hour);
                        $time_office=$full_trabajo;
                      
                     
                        $hourjornada= $exit->diff($entry)->format('%H:%I:%S');
                        $jornfood= $food2->diff($food1)->format('%H:%I:%S');
                       
                        $a=new DateTime($hourjornada);
                        $b=new DateTime($jornfood);
                        if($hora_comida2!="00:00:00" ){
                            $time_trabajado=  $full->diff($com)->format('%H:%I:%S');
                        }else if($hora_salida !="00:00:00"){
                            $time_trabajado=  $full->diff($b)->format('%H:%I:%S');
                            
                        }
                        $fulljorn= $a->diff($b)->format('%H:%I:%S');
                        
                        //Validar Tiempo Extra
 
                        $time_extra=new DateTime($time_trabajado);
                        $trabajado= new DateTime($fulljorn);
                        $trabajado_tole= new DateTime("08:11:00");
                        // $extra=($time_extra> $trabajado_tole)?"Extra":(($time_extra<$trabajado)?"Incompleta":"Completa");
                        $extra=(($time_extra<$trabajado)?"Jornada Incompleta":"Jornada Completa");
                        $difWork=$trabajado->diff($time_extra)->format('%R%H:%I:%S');

                        //Politicas de horarios flexibles ********************************
                        //validacion Horario para en caso contrario aumentar tiempo de entrada/salida
                        // $tope="09:30";
                        
                        // if($entry_hour=="08:00" || $entry_hour=="08:30" || $entry_hour=="09:00"){
                        //  while ($h_ent > new DateTime($entry_hour) ) {
                        //     if( $h_ent <= new DateTime($tope)){
                        //         $entry_hour= date('H:i', strtotime('+30 minutes',strtotime($entry_hour)));
                        //         $exit_hour= date('H:i', strtotime('+30 minutes',strtotime($exit_hour)));
                        //     }else{
                        //         break;
                        //     }
                        //  }
                        // }
                }
                
               
                
                //convertimos horarios de entrada con formato de politica de asistencia
                $npuntualidad=date('H:i:s', strtotime($punt,strtotime($entry_hour)));
                $entAnticipada1=date('H:i:s', strtotime('-60 minute',strtotime($entry_hour)));
                $entAnticipada2=date('H:i:s', strtotime('-120 minute',strtotime($entry_hour)));
                $nRetardo=date('H:i:s', strtotime($retardo,strtotime($entry_hour)));
                $nToleracia=date('H:i:s', strtotime($tol,strtotime( $entry_hour)));
                $nSuspension=date('H:i:s', strtotime($susp,strtotime($entry_hour)));
                $ret=new DateTime( $nRetardo);
                $puntu=new DateTime($npuntualidad);
                $tole=new DateTime($nToleracia);
                $ant1=new DateTime($entAnticipada1);
                $ant2=new DateTime($entAnticipada2);
                $suspe=new DateTime($nSuspension);

          
            //Politicas de Asistencia para estatus reporte
            if($h_ent >$suspe){
                $estatus="Suspensión";
                }else if($h_ent >$ret && $h_ent <=$suspe){
                    $estatus="Retardo";
                }else if($h_ent > $puntu && $h_ent<= $tole){
                    $estatus="Tolerancia";
                }else if($h_ent <=$puntu && $h_ent !=""){
                    $estatus="Puntualidad";
                }
                //PERMISOS
                $objJust = PersonalJustification::where("date",$inicio)->where("user",$pers->usuario_ad)->where("status",1)->first();
                if($objJust != ""){
                    $estatus="Justificado";
                    $exc.="Justificado";
                }else{

                $objVac = Vacation::where(function ($query) use($inicio) {
                    $query->whereDate("start_date","<=",$inicio)
                    ->whereDate("end_date",">=",$inicio);
                })->where("personal_intelisis_usuario_ad",$pers->usuario_ad)->where("status","autorizado")->select("document")->first();
                if($objVac != ""){
                    $estatus="Vacaciones";
                    $exc.="Vacaciones";
                    $document= url($objVac->document);
                }else{

                $objPerm = WorkPermit::with('type_permit')->where(function ($query) use($inicio) {
                    $query->whereDate("start_date","<=",$inicio)
                    ->whereDate("end_date",">=",$inicio);
                })->where("personal_intelisis_usuario_ad",$pers->usuario_ad)->where("status","autorizado")->first();
                if($objPerm != ""){
                    $estatus="Permiso";
                    $exc.="Permiso"." ".$objPerm->type_permit->description;
                    $document= url($objPerm->document);
                }
             }
            }

                    
                    array_push($objReporte,(object)[
                        "personal_id" => $pers->personal_id,
                        "previous_personal_id" => $pers->previous_personal_id,
                        "deparment" => $pers->deparment,
                        "location" => $pers->location,
                        "payment_period" => $pers->payment_period,
                        "name" => $pers->name.' '.$pers->last_name,
                        "entrada"=> $hora_entrada,
                        "comida"=> $hora_comida1.' - '.$hora_comida2,
                        "salida"=> $hora_salida,
                        "fecha"=> Carbon::parse($inicio)->format("d/m/Y"),
                        "timecomida"=>$time_comidas,
                        "trabajo" =>$time_trabajado,
                        "trabajado" =>$fulljorn,
                        "timeoffice"=>  $time_office,
                        "extra" =>$extra,
                        "diff" => $difWork,
                        "ndia" =>  $nDia,
                        "horariobase" => $entry_hour.' - '.$exit_food.' '.$entry_food.' - '.$exit_hour,
                        "hourentry" => $entry_hour,
                        "hourfood1" => $exit_food,
                        "hourefood2" => $entry_food,
                        "hourexit" => $exit_hour,
                        "estatusjornada" => $estatus,
                        "estatus" => $estatus,
                        "justification" => $objJust,
                        "document" => $document,
                        "message" => $msg,
                        "c1" => $c1,
                        "c2" => $c2,
                        "c3" => $c3,
                        "c4" => $c4,
                        "c5" => $c5,
                        "c6" => $c6,
                        "c7" => $c7,
                        "exc"=>$exc,
                     ]);
                    }
                }
                     $inicio = strtotime ( '+1 day' , strtotime ( $inicio ) ) ;
                    $inicio = date ( 'Y-m-d' , $inicio );

                }
            }
        }
    } catch (\Throwable $th) {
        return $th;
    }
            return response()->json($objReporte,200);
    }

     protected function getPersonalAttendance(Request $request){
        
        $datos= $this->validate(request(),[
        
            'date' => 'required', 
            ]);
            $objReporte=[];
            $tol="";
            $punt="";
            $retardo="";
            $susp="";
            $personal = PersonalIntelisis::where('usuario_ad',auth()->user()->usuario)->where('status','ALTA')->first();
         
            $inicio=$datos["date"].'-01';
            $month=explode("-",$datos["date"]);
            $d=cal_days_in_month(CAL_GREGORIAN, $month[1], $month[0]);
            $fin=$datos["date"]."-".$d;
            $policy=AttendancePolicy::where('location',$personal->location)->first();
            $tol= '+'.$policy->tolerance.' minute';
            $punt= '+'.$policy->puntuality.' minute';
            $susp= '+'.$policy->suspension.' minute';
            $retardo= '+'.$policy->delay.' minute';
            $arrayCheck= $this->getAsistenciaMes($datos["date"],$personal->previous_personal_id,$personal->rfc);
            // 
            // foreach($arrayChec as $check){
            //     $inicio=$check->fecha;
            // }

            while($inicio <= $fin){
               $arrayChec= $this->arrayfilter($arrayCheck, $inicio);
                //Obtenemos la informacion de horario  del empleado
                // $arrayChec= $this->Fecha_Checada($inicio,$personal->previous_personal_id);
                sort($arrayChec);
                $dia_fecha = date("N",strtotime($inicio));
                $nDia= $dia_fecha;
                $hora_comida1="--";
                $hora_comida2="--";
                $hora_entrada="--";
                $hora_salida="--";
                $time_comidas="00:00:00";
                $time_trabajado="00:00:00";
                $trabajado="08:00:00";
                $full_trabajo="00:00:00";
                $extra="Suspensión";
                $estatus="Suspensión";
                $vspuntualidad="";
                $h_ent="";
                //Obtenemos la informacion de horario  del empleado
                // $personal_time= PersonalTime::where('user',$personal->usuario_ad)->where('dmirh_cat_time_status_id',1)->whereDate("start_date","<=",$inicio)->orderBy("id","desc")->first();
                $personal_time= PersonalTime::where('user',$personal->usuario_ad)->where('dmirh_cat_time_status_id',1)->whereDate("start_date","<=",$inicio)
                ->where(function ($query) {
                    $query->where('active', '=', 1)
                          ->orWhere('active', '=', 0);
                })->orderBy("start_date","desc")->first();
               
                if( $personal_time!= ""){
                //obtenemos el detalle dia especifico del horario
                $time_detail= PersonalTimeDetail::where('dmirh_personal_time_id',$personal_time->id)->where('week_day',$dia_fecha)->first();

                if($time_detail != ""){
                    //Asignacion hora y salida de empleado
                $entry_hour = Carbon::parse($time_detail->entry_hour)->format('H:i');
                $exit_food = Carbon::parse($time_detail->exit_food_hour)->format('H:i');
                $entry_food = Carbon::parse($time_detail->entry_food_hour)->format('H:i');
                $exit_hour = Carbon::parse($time_detail->exit_hour)->format('H:i');
                       //validamos la checadas en attendance    
                       if($arrayChec){
                        if(count($arrayChec)>3){
                            $fechahora_entrada= current($arrayChec);
                            $hora_entrada=$fechahora_entrada;
                            $com_entra= date('H:i:s',strtotime("+5 minute", strtotime($hora_entrada)));
                            $n= new DateTime($com_entra);
                            $com_next=next($arrayChec);
                            $hora_next=$com_next;
                            $n2= new DateTime($hora_next);
     
                            if($n2< $n){
                                $fechahora_comida1=next($arrayChec);
                            }else{
                                $fechahora_comida1=current($arrayChec);
                            }
                            $hora_comida1= $fechahora_comida1;
                            $com_comi1=date('H:i:s',strtotime("+2 minute", strtotime($hora_comida1)));
                            $c= new DateTime($com_comi1);
                            $com_next1=next($arrayChec);
                            $hora_next=$com_next1;
                            $c2= new DateTime($hora_next);
                            if($c2< $c){
                            $fechahora_comida2=next($arrayChec);
                            }else{
                                $fechahora_comida2=current($arrayChec);
                            }
                            $fechahora_salida= end($arrayChec);
                            $hora_comida2= $fechahora_comida2;
                            $hora_salida=$fechahora_salida;
     
                        }else if(count($arrayChec)==3){
                            $fechahora_entrada= current($arrayChec);
                            $fechahora_comida1=next($arrayChec);
                            $fechahora_salida= end($arrayChec);
                            $hora_entrada=$fechahora_entrada;
                            $hora_comida1=$fechahora_comida1;
                            $hora_comida2=date("H:i:s",strtotime("00:00:00"));
                            $hora_salida=$fechahora_salida;
                        }else if(count($arrayChec)==2){
                            $fechahora_entrada= current($arrayChec);
                            $fechahora_salida= end($arrayChec);
                            $hora_entrada=$fechahora_entrada;
                            $hora_comida1=date("H:i:s",strtotime("00:00:00"));
                            $hora_comida2=date("H:i:s",strtotime("00:00:00"));
                            $hora_salida=$fechahora_salida;
                        }else{
                            $fechahora_entrada= current($arrayChec);
                            $hora_entrada=$fechahora_entrada;
                            $hora_comida1=date("H:i:s",strtotime("00:00:00"));
                            $hora_comida2=date("H:i:s",strtotime("00:00:00"));
                            $hora_salida= date("H:i:s",strtotime("00:00:00"));
                        }
     
                        $h_ent= new DateTime($hora_entrada);
                        $h_sal= new DateTime($hora_salida);
                        
                        $f1 = new DateTime($hora_comida1);
                        $f2 = new DateTime($hora_comida2);
     
                        if($hora_comida1 !="00:00:00" && $hora_comida2 !="00:00:00" ){
                            $time_comids= $f1->diff($f2);
                            $time_comidas= $time_comids->format('%H:%I:%S');
                        }
                       
                        if($hora_entrada !="00:00:00" && $hora_salida !="00:00:00" ){
                            $hora_trabajo=$h_ent->diff($h_sal);
                            $full_trabajo=$hora_trabajo->format('%H:%I:%S');
                        }
                       
                            $full=  new DateTime($full_trabajo);
                            $com=  new DateTime($time_comidas);
     
                            $time_trabajado=  $full->diff($com)->format('%H:%I:%S');
     
                            //Validar Tiempo Extra
       
                            $time_extra=new DateTime($time_trabajado);
                            $trabajado= new DateTime("08:00:00");
                            $trabajado_tole= new DateTime("08:11:00");
                            $extra=($time_extra> $trabajado_tole)?"Extra":(($time_extra<$trabajado)?"Incompleta":"Completa");
                        
    
                    
                   
                    
                    //convertimos horarios de entrada con formato de politica de asistencia
                    $npuntualidad=date('H:i:s', strtotime($punt,strtotime($entry_hour)));
                    $entAnticipada1=date('H:i:s', strtotime('-60 minute',strtotime($entry_hour)));
                    $entAnticipada2=date('H:i:s', strtotime('-120 minute',strtotime($entry_hour)));
                    $nRetardo=date('H:i:s', strtotime($retardo,strtotime($entry_hour)));
                    $nToleracia=date('H:i:s', strtotime($tol,strtotime( $entry_hour)));
                    $nSuspension=date('H:i:s', strtotime($susp,strtotime($entry_hour)));
                    $ret=new DateTime( $nRetardo);
                    $puntu=new DateTime($npuntualidad);
                    $tole=new DateTime($nToleracia);
                    $ant1=new DateTime($entAnticipada1);
                    $ant2=new DateTime($entAnticipada2);
                    $suspe=new DateTime($nSuspension);
    
              $class="";
                //Politicas de Asistencia para estatus reporte
                if($h_ent >$suspe){
                    $class="suspension";
                    }else if($h_ent >$ret && $h_ent <=$suspe){
                        $class="retardo";
                    }else if($h_ent > $puntu && $h_ent<= $tole){
                        $class="tolerancia";
                    }else if($h_ent <=$puntu && $h_ent !=""){
                        $class="puntualidad";
                    }
                    
               
                        // array_push($objReporte,(object)[
                        //     "title"=> "Entrada: ". $hora_entrada,
                        //     "comida"=> $hora_comida1.' - '.$hora_comida2,
                        //     "salida"=> $hora_salida,
                        //     "startDate"=> $inicio,
                        //     "trabajo" =>$time_trabajado,
                        //     "estatus" => $estatus,
                        //  ]);
                        array_push($objReporte,(object)[
                            "title"=> "Entrada: ". $hora_entrada,
                            // "comida"=> $hora_comida1.' - '.$hora_comida2,
                            // "salida"=> $hora_salida,
                            "startDate"=> $inicio,
                            "classes" => $class,
                         ]);
                         array_push($objReporte,(object)[
                            "title"=> "Comida: ". $hora_comida1.' - '.$hora_comida2,
                            "startDate"=> $inicio,
                            // "estatus" => $estatus,
                            "classes" => $class,
                         ]);
                         array_push($objReporte,(object)[
                            "title"=> "Salida: ". $hora_salida,
                            "startDate"=> $inicio,
                            // "estatus" => $estatus,
                            "classes" => $class,
                         ]);
                        }

                         $objJust = PersonalJustification::where("date",$inicio)->where("user",$personal->usuario_ad)->where("status",1)->first();
                         if($objJust != ""){
                            array_push($objReporte,(object)[
                                "title"=> "Justificado",
                                "startDate"=> $inicio,
                                "obj" => $objJust,
                                // "classes" => $class,
                             ]);
                            //  $estatus="Justificado";
                         }else{
                            $objVac = Vacation::where(function ($query) use($inicio) {
                                $query->whereDate("start_date","<=",$inicio)
                                ->whereDate("end_date",">=",$inicio);
                            })->where("personal_intelisis_usuario_ad",$personal->usuario_ad)->where("status","autorizado")->select("document")->first();
                            if($objVac != ""){
                                array_push($objReporte,(object)[
                                    "title"=> "Vacaciones",
                                    "startDate"=> $inicio,
                                    "obj" => url($objVac->document),
                                    // "classes" => $class,
                                 ]);
                            }else{
            
                            $objPerm = WorkPermit::where(function ($query) use($inicio) {
                                $query->whereDate("start_date","<=",$inicio)
                                ->whereDate("end_date",">=",$inicio);
                            })->where("personal_intelisis_usuario_ad",$personal->usuario_ad)->where("status","autorizado")->select("document")->first();
                            if($objPerm != ""){
                                $estatus="Permiso";
                                array_push($objReporte,(object)[
                                    "title"=> "Permiso",
                                    "startDate"=> $inicio,
                                    "obj" => url($objPerm->document),
                                    // "classes" => $class,
                                 ]);
                            }
                         }
                        
                        
                         }
                    }
                }

                    $inicio = strtotime ( '+1 day' , strtotime ( $inicio ) ) ;
                    $inicio = date ( 'Y-m-d' , $inicio );
                }
            
                        return response()->json($objReporte,200);
     
    }  

    function arrayfilter($elements, $query) {
        $found = [];
        foreach($elements as $element) {

          if(strpos($element->fecha, $query) !== false) {
            array_push($found, $element->checada);
          }
        }
        return $found;
    } 
    function getAsistenciaMes($fech,$pers,$rfc){

        $serverName = "192.168.10.72\AM,7788"; //serverName\instanceName
        $connectionInfo = array( "Database"=>"CA", "UID"=>"intraUsr", "PWD"=>"1Em1KDBi");
        $conn_checador = sqlsrv_connect( $serverName, $connectionInfo);

        if($conn_checador ) {
            // echo "Conexión establecida.<br />";
        }else{
            echo "Conexión no se pudo establecer.<br />";
            die( print_r( sqlsrv_errors(), true));
        }
        $fecha=$fech;
        $persona=$pers;
        /* $sql_horario = "select
        (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
        (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
            from  [dbo].[vw_Attendance_unionB] as c
            where  c.SSN =  '".$persona."'
            and	CONVERT(VARCHAR(25), c.CHECKTIME, 126) like '%$fecha%' 
            order by fecha asc";

            $newQueryRFC="select
            (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
            (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
        from  [dbo].[vw_Attendance_unionB] as c
        where  c.SSN = '".$rfc."'
        and	CONVERT(VARCHAR(25), c.CHECKTIME, 126) like '%$fecha%' 
            order by fecha asc"; */
        
        /*  */
        $sql_horario = "select
                            (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
                            (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
                        from  [dbo].[vw_Attendance_unionB] as c
                        where  c.SSN =  '".$persona."'and c.CHECKTIME like '%$fecha%' 
                        order by fecha asc";

        $newQueryRFC="select
                            (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
                            (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
                    from  [dbo].[vw_Attendance_unionB] as c
                    where  c.SSN = '".$rfc."'
                    and	c.CHECKTIME like '%$fecha%' 
                        order by fecha asc";

        /*  */

        $stmt = sqlsrv_query( $conn_checador, $sql_horario );
        $stmt2 = sqlsrv_query( $conn_checador, $newQueryRFC );
        $arr =[];
        $arr2 =[];

        while ($row = sqlsrv_fetch_array($stmt))
        {
           $hora_chec=explode(".",$row["checada"]);

           array_push($arr,(object)[
            "fecha"=>$row["fecha"],
            "checada"=> $hora_chec[0]
           ]);
        }
        while ($row = sqlsrv_fetch_array($stmt2))
        {
           $hora_chec=explode(".",$row["checada"]);

           array_push($arr2,(object)[
            "fecha"=>$row["fecha"],
            "checada"=> $hora_chec[0]
           ]);
        }

        $mergedArray = array_merge($arr, $arr2);
    return $mergedArray;        

    }

    public $salida = array();
    protected function getReporteAsistenciaMiPersonal(Request $request){

        $datos= $this->validate(request(),[
        
            'fechas' => 'required', 
            ]);
            $objReporte=[];
            $now=  Carbon::parse($datos["fechas"]);
            // $arrayFechas= explode(",",$datos["fechas"]);
            $usuario =auth()->user()->usuario;
            $weekStartDate = $now->startOfWeek()->format('Y-m-d');
            $weekEndDate = $now->endOfWeek()->format('Y-m-d');
            $inicio= $weekStartDate;
            $fin=$weekEndDate;
            $tol="";
            $punt="";
            $retardo="";
            $susp="";
            $plaza= PersonalIntelisis::where("usuario_ad",$usuario)->where("status","ALTA")->select("plaza_id","top_plaza_id","location")->first();
            $objHorarios=[];
           // informacion de vista de plazas personal
            $res = PersonalIntelisis::with('staffall')->where("usuario_ad",Auth::user()->usuario)->where('status', '!=', "BAJA")->get();
           //obtenemos salida de personal
            $this->encuentraParents($res,$salida,null);

            if($salida != null){
        foreach ( $salida as $pers) {
            if($inicio> $fin){
                $inicio=$weekStartDate;
            }

            $policy=AttendancePolicy::where('location',$pers->location)->first();

                $tol= '+'.$policy->tolerance.' minute';
                $punt= '+'.$policy->puntuality.' minute';
                $susp= '+'.$policy->suspension.' minute';
                $retardo= '+'.$policy->delay.' minute';

            $arrayCheck= $this->Fecha_Checada_All($inicio,$fin,$pers->previous_personal_id,$pers->rfc);
            while($inicio <= $fin){
                $array=[];
             $arrayChec=[];
               foreach ($arrayCheck as $value) {
                    if($inicio == $value["fecha"]){
                        $array= explode(".",$value[1]);
                       array_push($arrayChec,$array[0]);
                    }
               }
               sort($arrayChec);
                $dia_fecha = date("N",strtotime($inicio));
                $nDia= $dia_fecha;
                $hora_comida1="--";
                $hora_comida2="--";
                $hora_entrada="--";
                $hora_salida="--";
                $document="";
                $time_comidas="00:00:00";
                $time_trabajado="00:00:00";
                $trabajado="08:00:00";
                $full_trabajo="00:00:00";
                $extra="Suspensión";
                $estatus="Suspensión";
                $vspuntualidad="";
                $h_ent="";
                //Obtenemos la informacion de horario  del empleado
                // $personal_time= PersonalTime::where('user',$pers->usuario_ad)->where('dmirh_cat_time_status_id',1)->orderBy('created_at', 'desc')->first();
                $personal_time= PersonalTime::where('user',$pers->usuario_ad)->where('dmirh_cat_time_status_id',1)
                ->whereDate("start_date","<=",$inicio)
                ->where(function ($query) {
                    $query->where('active', '=', 1)
                          ->orWhere('active', '=', 0);
                })->orderBy("start_date","desc")->first();

                if( $personal_time!= ""){
                //obtenemos el detalle dia especifico del horario
                $time_detail= PersonalTimeDetail::where('dmirh_personal_time_id',$personal_time->id)->where('week_day',$dia_fecha)->first();

                if($time_detail != ""){
                    //Asignacion hora y salida de empleado
                $entry_hour = Carbon::parse($time_detail->entry_hour)->format('H:i');
                $exit_hour = Carbon::parse($time_detail->exit_hour)->format('H:i');
                $exit_food = Carbon::parse($time_detail->exit_food_hour)->format('H:i');
                $entry_food = Carbon::parse($time_detail->entry_food_hour)->format('H:i');
                $exit_food_tol=date('H:i:s',strtotime("-60 minute", strtotime($exit_food)));
                $entry_food_tol=date('H:i:s',strtotime("+60 minute", strtotime($entry_food)));
                // $exit_food_tole=new DateTime($exit_food_tol);
             //validamos la checadas en attendance    
                if($arrayChec){

                    $fechahora_entrada= current($arrayChec);
                    $fechahora_salida= end($arrayChec);
                    $hora_entrada=$fechahora_entrada;
                    $hora_comida1=date("H:i:s",strtotime("00:00:00"));
                    $hora_comida2=date("H:i:s",strtotime("00:00:00"));
                    $hora_salida=$fechahora_salida;
                    $arrFoods=[];
                    foreach ($arrayChec as $key => $hour) {
                        if($hour>=  $exit_food_tol && $hour <= $entry_food_tol){
                            array_push($arrFoods,$hour);
                        }
                    }
                    if($arrFoods != null){
                        if(count($arrFoods)>1){
                        $hora_comida1=current($arrFoods);
                        $hora_comida2= end($arrFoods);
                        }else{
                            $hora_comida1=current($arrFoods);
                            $hora_comida2=date("H:i:s",strtotime("00:00:00"));
                        }
                    }

 
                    $h_ent= new DateTime($hora_entrada);
                    $h_sal= new DateTime($hora_salida);
                    
                    $f1 = new DateTime($hora_comida1);
                    $f2 = new DateTime($hora_comida2);
 
                    if($hora_comida1 !="00:00:00" && $hora_comida2 !="00:00:00" ){
                        $time_comids= $f1->diff($f2);
                        $time_comidas= $time_comids->format('%H:%I:%S');
                    }
                   
                    if($hora_entrada !="00:00:00" && $hora_salida !="00:00:00" ){
                        $hora_trabajo=$h_ent->diff($h_sal);
                        $full_trabajo=$hora_trabajo->format('%H:%I:%S');
                    }
                   
                        $full=  new DateTime($full_trabajo);
                        $com=  new DateTime($time_comidas);
 
                        $time_trabajado=  $full->diff($com)->format('%H:%I:%S');
 
                        //Validar Tiempo Extra
 
                        $time_extra=new DateTime($time_trabajado);
                        $trabajado= new DateTime("08:00:00");
                        $trabajado_tole= new DateTime("08:11:00");
                        $extra=($time_extra> $trabajado_tole)?"Extra":(($time_extra<$trabajado)?"Incompleta":"Completa");
                    

                        //Politicas de horarios flexibles ********************************
                        //validacion Horario para en caso contrario aumentar tiempo de entrada/salida
                        // $tope="09:30";
                        
                        // if($entry_hour=="08:00" || $entry_hour=="08:30" || $entry_hour=="09:00"){
                        //  while ($h_ent > new DateTime($entry_hour) ) {
                        //     if( $h_ent <= new DateTime($tope)){
                        //         $entry_hour= date('H:i', strtotime('+30 minutes',strtotime($entry_hour)));
                        //         $exit_hour= date('H:i', strtotime('+30 minutes',strtotime($exit_hour)));
                        //     }else{
                        //         break;
                        //     }
                        //  }
                        // }
                }
                
               
                
                //convertimos horarios de entrada con formato de politica de asistencia
                $npuntualidad=date('H:i:s', strtotime($punt,strtotime($entry_hour)));
                $entAnticipada1=date('H:i:s', strtotime('-60 minute',strtotime($entry_hour)));
                $entAnticipada2=date('H:i:s', strtotime('-120 minute',strtotime($entry_hour)));
                $nRetardo=date('H:i:s', strtotime($retardo,strtotime($entry_hour)));
                $nToleracia=date('H:i:s', strtotime($tol,strtotime( $entry_hour)));
                $nSuspension=date('H:i:s', strtotime($susp,strtotime($entry_hour)));
                $ret=new DateTime( $nRetardo);
                $puntu=new DateTime($npuntualidad);
                $tole=new DateTime($nToleracia);
                $ant1=new DateTime($entAnticipada1);
                $ant2=new DateTime($entAnticipada2);
                $suspe=new DateTime($nSuspension);

          
            //Politicas de Asistencia para estatus reporte
            if($h_ent >$suspe){
                $estatus="Suspensión";
                }else if($h_ent >$ret && $h_ent <=$suspe){
                    $estatus="Retardo";
                }else if($h_ent > $puntu && $h_ent<= $tole){
                    $estatus="Tolerancia";
                }else if($h_ent <=$puntu && $h_ent !=""){
                    $estatus="Puntualidad";
                }
                
                $objJust = PersonalJustification::where("date",$inicio)->where("user",$pers->usuario_ad)->where("status",1)->first();
                if($objJust != ""){
                    $estatus="Justificado";
                }else{

                $objVac = Vacation::where(function ($query) use($inicio) {
                    $query->whereDate("start_date","<=",$inicio)
                    ->whereDate("end_date",">=",$inicio);
                })->where("personal_intelisis_usuario_ad",$pers->usuario_ad)->where("status","autorizado")->select("document")->first();
                if($objVac != ""){
                    $estatus="Vacaciones";
                    $document= url($objVac->document);
                }else{

                $objPerm = WorkPermit::where(function ($query) use($inicio) {
                    $query->whereDate("start_date","<=",$inicio)
                    ->whereDate("end_date",">=",$inicio);
                })->where("personal_intelisis_usuario_ad",$pers->usuario_ad)->where("status","autorizado")->select("document")->first();
                if($objPerm != ""){
                    $estatus="Permiso";
                    $document= url($objPerm->document);
                }
             }
            }
                    array_push($objReporte,(object)[
                        "personal_id" => $pers->personal_id,
                        "previous_personal_id" => $pers->previous_personal_id,
                        "deparment" => $pers->deparment,
                        "location" => $pers->location,
                        "payment_period" => $pers->payment_period,
                        "name" => $pers->name,
                        "last_name" => $pers->last_name,
                        "entrada"=> $hora_entrada,
                        "comida"=> $hora_comida1.' - '.$hora_comida2,
                        "salida"=> $hora_salida,
                        "fecha"=> Carbon::parse($inicio)->format("d/m/Y"),
                        "timecomida"=>$time_comidas,
                        "trabajo" =>$time_trabajado,
                        "extra" =>$extra,
                        "ndia" =>  $nDia,
                        "horariobase" => $entry_hour.' - '.$exit_hour,
                        "estatus" => $estatus,
                        "justification" => $objJust,
                        "document" => $document

                     ]);
                    }
                }
                     $inicio = strtotime ( '+1 day' , strtotime ( $inicio ) ) ;
                    $inicio = date ( 'Y-m-d' , $inicio );

                }
            }
        }
        

            return response()->json($objReporte,200);
    }
    function encuentraParents($entrada, &$salida, $padre) {
        // para cada elemento del array 
        foreach($entrada as $valor) {
            // añade una entrada al array de salida indicando su id y el de su padre
            // if($padre!=null ){
            //     $salida[] = $valor;
            // }
            if($padre!=null ){
                if(isset($valor["plaza_id"]) && isset($valor["personal_id"])){
                  $salida[] = $valor;
    
                }
          }
    
            // si el elemento tiene children
            if (isset($valor["staffall"] ) ) {
                // procesa los hijos recursivamente indicando el id del padre
               $this->encuentraParents( $valor["staffall"], $salida, $valor["plaza_id"] );
            }
        }
    }
    function Fecha_Checada($fech,$pers){

        $serverName = "192.168.10.72\AM,7788"; //serverName\instanceName
        $connectionInfo = array( "Database"=>"CA", "UID"=>"intraUsr", "PWD"=>"1Em1KDBi");
        $conn_checador = sqlsrv_connect( $serverName, $connectionInfo);

        if($conn_checador ) {
            // echo "Conexión establecida.<br />";
        }else{
            echo "Conexión no se pudo establecer.<br />";
            die( print_r( sqlsrv_errors(), true));
        }
        $fecha=$fech;
        $persona=$pers;
        $newQuery="select
                (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
                (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
            from  [dbo].[vw_Attendance_unionB] as c
            where  c.SSN like '".$persona."'
            and c.CHECKTIME BETWEEN  '$fecha 00:00:00:000' AND '$fecha 23:59:59:999'
            order by fecha,checada asc";

        // $sql_horario = "select 
        //         (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
        //         (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
        //     from CHECKINOUT as c 
        //     inner join USERINFO as u
        //     on c.USERID = u.USERID
        //     where  u.SSN = '".$persona."'
        //     and	c.CHECKTIME BETWEEN  '$fecha 00:00:00:000' AND '$fecha 23:59:59:999'
        //     order by fecha asc";

        $stmt = sqlsrv_query( $conn_checador, $newQuery );
        $arr = array();

        while ($row = sqlsrv_fetch_array($stmt))
        {
           $hora_chec=explode(".",$row["checada"]);
           $arr[] = $hora_chec[0];
        }
    
    return $arr;        

    }

    protected function addDaysOff(){
        $datos= $this->validate(request(),[
        
            'date' => 'required', 
            ]);
      
          if(Auth::check()){    
      
              try {
                $day= new CatDaysOff();
           
             $day->date= $datos["date"];
             $day->save();
    
              } catch (\Throwable $th) {
                return response()->json(['error'=>'Error al insertar.'],500);
              }
            
          
             return response()->json(['success'=>' Fecha agregado Correctamente.'],200);
      }else{
      
        return response()->json(['error'=>'No tienes Sesion'],500);
        }
    }

    protected function getDaysOff(){
        if(Auth::check()){
          $res= CatDaysOff::all();
              
          return response()->json($res, 200); 
        }else{
  
          return response()->json(['error'=>'No tienes Sesion'],200);
          }
      
      }
      protected function updateDaysOff(){
        $datos= $this->validate(request(),[
          
          'id' => 'required', 
          'date' => 'required', 
        //   'deleted' => 'required', 
          ]);
    
        if(Auth::check()){    
    
          try {
              $days= CatDaysOff::find($datos["id"]);
         
            $days->date= $datos["date"];
            // $days->deleted= $datos["deleted"];
            $days->save();
  
            } catch (\Throwable $th) {
              return response()->json(['error'=>'Error al modificar.'],500);
            }
          
        
           return response()->json(['success'=>'Fecha Modificado Correctamente.'],200);
      }else{
      
        return response()->json(['error'=>'No tienes Sesion'],200);
        }
      
      }

    function Fecha_Checada_All($fech,$fin,$pers,$rfc){

        $serverName = "192.168.10.72\AM,7788"; //serverName\instanceName
        $connectionInfo = array( "Database"=>"CA", "UID"=>"intraUsr", "PWD"=>"1Em1KDBi");
        $conn_checador = sqlsrv_connect( $serverName, $connectionInfo);

        if($conn_checador ) {
            // echo "Conexión establecida.<br />";
        }else{
            echo "Conexión no se pudo establecer.<br />";
            die( print_r( sqlsrv_errors(), true));
        }
        $fecha=$fech;
        $persona=$pers;
        $rfc=$rfc;
        $newQuery="select
                (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
                (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
            from  [dbo].[vw_Attendance_unionB] as c
            where  c.SSN like '".$persona."'
            and c.CHECKTIME BETWEEN  '$fecha 00:00:00:000' AND '$fin 23:59:59:999'
            order by fecha,checada asc";

            $newQueryRFC="select
                (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
                (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
            from  [dbo].[vw_Attendance_unionB] as c
            where  c.SSN like '".$rfc."'
            and c.CHECKTIME BETWEEN  '$fecha 00:00:00:000' AND '$fin 23:59:59:999'
            order by fecha,checada asc";

        // $sql_horario = "select 
        //         (CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
        //         (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada
        //     from CHECKINOUT as c 
        //     inner join USERINFO as u
        //     on c.USERID = u.USERID
        //     where  u.SSN = '".$persona."'
        //     and	c.CHECKTIME BETWEEN  '$fecha 00:00:00:000' AND '$fecha 23:59:59:999'
        //     order by fecha asc";

        $stmt = sqlsrv_query( $conn_checador, $newQuery );
        $stmt2 = sqlsrv_query( $conn_checador, $newQueryRFC );
        $arr = array();
        $arr2 = array();
        if( $stmt === false ) {
            if( ($errors = sqlsrv_errors() ) != null) {
                foreach( $errors as $error ) {
                    echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
                    echo "code: ".$error[ 'code']."<br />";
                    echo "message: ".$error[ 'message']."<br />";
            }
        }
       }else{
       
        while ($row = sqlsrv_fetch_array($stmt))
        {
           $arr[] = $row;
        }

        while ($row = sqlsrv_fetch_array($stmt2))
        {
           $arr2[] = $row;
        }

        $mergedArray = array_merge($arr, $arr2);
    }
    
    return $mergedArray;        

    }


    protected function AddUsersBioTime(){

        $serverName = "192.168.3.229\DWH,1433"; //serverName\instanceName
        $connectionInfo = array( "Database"=>"zkbiotime", "UID"=>"ialtuzar", "PWD"=>"ua3sf6ai");
        $conn_checador = sqlsrv_connect( $serverName, $connectionInfo);

        if($conn_checador ) {
            // echo "Conexión establecida.<br />";
        }else{
            echo "Conexión no se pudo establecer.<br />";
            die( print_r( sqlsrv_errors(), true));
        }
        $contador=350;
        $personal= PersonalIntelisis::where("status","ALTA")->where("location","ANDARES")->get();


        foreach ($personal as $user) {

            $newQuery="insert into [dbo].[personnel_employee] (status,emp_code,first_name,last_name,enroll_sn,ssn,dev_privilege,is_admin,enable_att,enable_payroll,enable_overtime,enable_holiday,deleted,is_active,app_status,app_role,company_id,department_id)
             VALUES(0,".$contador.",'".$user->name."','".$user->last_name."','AEVL221061984','".$user->rfc."',0,0,1,1,0,1,0,1,0,1,1,6)";

            $stmt = sqlsrv_query( $conn_checador, $newQuery );

            if( $stmt === false ) {
                if( ($errors = sqlsrv_errors() ) != null) {
                    foreach( $errors as $error ) {
                        echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
                        echo "code: ".$error[ 'code']."<br />";
                        echo "message: ".$error[ 'message']."<br />";
                            }
                    
                        }
                    }
                    $contador++;
                }

                return $stmt;

    }

    public function lastCheck(Request $request){
        //return ["success" => 1, "request" => $request->input()];

        if(isset($request->user)){
            $personal_intelisis = PersonalIntelisis::where("usuario_ad",$request->user)->where("status","ALTA")->first();

            if($personal_intelisis != null){

                $fecha = Carbon::now()->format("Y-m-d");

                $checks_personal_id= DB::connection('attendance_sqlsrv')
                                            ->table("vw_Attendance_unionB as c")
                                            ->where('c.SSN',$personal_intelisis->personal_id)
                                            ->where('c.CHECKTIME','like',"%$fecha%")
                                            ->orderBy('fecha','asc')
                                            ->orderBy('checada','asc')
                                            ->select(DB::raw("(CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
                                            (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada"))
                                            ->get();
                
                //En la nueva version se utiliza el rfc xq es el que utiliza el BioTime
                $checks_rfc= DB::connection('attendance_sqlsrv')
                                            ->table("vw_Attendance_unionB as c")
                                            ->where('c.SSN',$personal_intelisis->rfc)
                                            ->where('c.CHECKTIME','like',"%$fecha%")
                                            ->select(DB::raw("(CONVERT(VARCHAR(50), (CONVERT(DATE, c.CHECKTIME, 121)),121)) as fecha,
                                            (CONVERT(VARCHAR(50), (CONVERT(TIME, c.CHECKTIME, 121)),121)) as checada"))
                                            ->orderBy('fecha','asc')
                                            ->orderBy('checada','asc')
                                            ->get();
                
                $total_checks= $checks_personal_id->union($checks_rfc);

                if(sizeof($total_checks) > 0){
                    //Se obtiene el horario actual del usuario
                    /* $schedule = $this->getActiveSchedule($personal_intelisis->usuario_ad);

                    //return $schedule;
                    if(isset($schedule['success']) && $schedule['success'] == 1 && sizeof($total_checks) > 0){
                        $first_check_status = $this->firstCheckStatus($schedule['data'],$total_checks);
                        return ($first_check_status);
                    } */

                    return response()->json(["success"=> 1,"data"=>$total_checks,"message" => ""], 200);
                }else{
                    return response()->json(["success"=> 1,"data"=>null,"message" => "Sin registros"], 200);
                }
                
            }else{
                return response()->json(["success"=> 0,"message"=>"No existe el usuario solicitado."], 200);
            }

        }else{
            return response()->json(["success"=> 0,"message"=>"Error de parámetros"], 200);
        }



    }

    public function getActiveSchedule($_usuario_ad = null){

        if($_usuario_ad != null && $_usuario_ad != ""){
            $personal_intelisis = PersonalIntelisis::where("usuario_ad",$_usuario_ad)->where("status","ALTA")->first();
            $policy=AttendancePolicy::where('location',$personal_intelisis->location)
                                        ->where('deleted',0)
                                        ->first();

            
            $active_schedule = PersonalTime::join('dmirh_personal_time_detail','dmirh_personal_time_detail.dmirh_personal_time_id','=','dmirh_personal_time.id')
                                            ->where('dmirh_personal_time.user',$_usuario_ad)
                                            ->where('dmirh_personal_time.deleted',0)
                                            ->where('dmirh_personal_time.active',1)
                                            ->where('dmirh_personal_time_detail.deleted',0)
                                            ->select('dmirh_personal_time_detail.*')
                                            ->get();

            return ["success"=>1,
                    "data"=>[
                        "attendance_polity" => $policy,
                        "schedule" => $active_schedule,
                    ],
                    "message"=>""];
            
        }else{
            return ["success"=>0, "message"=>"Sin usuario."];
        }

    }

    public function firstCheckStatus($_schedule,$_total_checks){

        //Obtener la primera checada del día actual
        $current_date = Carbon::now()->format("Y-m-d");
        $first_check=null;

        for ($i=0; $i < sizeof($_total_checks); $i++) { 
            if($_total_checks[$i]->fecha ==  $current_date){
                $first_check = $_total_checks[$i];
                break;
            }
        }

        if($first_check != null){
            $attendance_polity = $_schedule['attendance_polity'];
            $schedule = $_schedule['schedule'];
            /* 
            $hora1 = new DateTime("2023-10-26 09:00:00");
            $hora2 = new DateTime("2023-10-26 10:30:00");
            */

            $status_of_checked = "";
            foreach ($schedule as $day) {
                if($day->week_day == date('N')){

                    $entry_hour = new DateTime($day->entry_hour);
                    //$puntual = date('H:i:s', strtotime("+ $day->entry_hour minute",strtotime($entry_hour)));
                    //$checada = new DateTime($first_check->checada);
                    $checada = new DateTime("08:30:01");

                    /* $hora = Carbon::parse('2023-10-26 15:30:00'); // Cambia la hora a la que deseas agregar un minuto
                    $hora->addMinutes(1); // Agrega un minuto

                    echo $hora->format('Y-m-d H:i:s'); */

                    if($checada < $entry_hour){

                    }

                    $hora_actual = date('Y-m-d H:i:s'); // Obtiene la fecha y hora actual en formato 'Y-m-d H:i:s'
                    $hora_especifica = '15:30:00'; // Hora que deseas establecer en formato 'H:i:s'

                    // Combina la fecha actual con la hora especificada
                    $fecha_hora = date('Y-m-d', strtotime($hora_actual)) . ' ' . $day->entry_hour;

                    dd($day,$checada,$day->entry_hour,$checada < $entry_hour,$fecha_hora);
                }
            }


            


            //return $_schedule['schedule'];
        }

        return ["success"=>1,"data"=>[$_schedule],"message"=>""];

    }
}
