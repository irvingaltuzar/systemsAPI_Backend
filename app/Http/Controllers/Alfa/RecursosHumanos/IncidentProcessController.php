<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis;
use App\Models\DmiRh\DmirhIncidentProcess;
use App\Models\DmiRh\DmirhIncidentProcessDetail;
use App\Models\DmiRh\DmirhIncidentProcessSuspention;
use App\Models\DmiRh\DmirhIncidentProcessMovERP;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\Alfa\RecursosHumanos\ReporteAsistenciaController;
use App\Models\DmiRh\DmirhPersonalJustification;
use App\Repositories\IntelisisRepository;
use App\Services\SendEmailService;
use App\Repositories\GeneralFunctionsRepository;
use App\Models\DmiControlSignaturesBehalf;
use App\Models\DmiControlProcedureValidation;
use App\Models\DmiRh\DmirhCatTypeJustification;
use App\Models\DmiControlUserSettings;
use App\Models\CatPaymentSchedule;
use App\Models\DmiRh\DmirhVacation;
use App\Models\Dmirh\DmirhWorkPermit;
use App\Models\VwDmiPersonalPlaza;


class IncidentProcessController extends Controller
{
    private $IntelisisRepository,$sendEmail,$GeneralFunctionsRepository;

    public function __construct(SendEmailService $_sendEmail, IntelisisRepository $_IntelisisRepository, GeneralFunctionsRepository $_GeneralFunctionsRepository){
        $this->IntelisisRepository = new IntelisisRepository();
        $this->sendEmail = $_sendEmail;
        $this->GeneralFunctionsRepository = $_GeneralFunctionsRepository;
        $this->movements_per_process=null;
        $this->validar_faltas_y_movimientos=null;
        $this->holidays=null;
        $this->list_collaborator_rfc=[];
        $this->staff_personal_intelisis_plaza_id=[];
        
    }
    
    public function listIncidentProcess(){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;

        $validate_coordinadora= DmiControlProcedureValidation::where('key','CAI_incident_proccess_coordinador_rr_hh-usuario_ad')
                                                                ->where('value',auth()->user()->usuario)
                                                                ->first();

        if($validate_coordinadora == null){
            //Es admin
            $list = DmirhIncidentProcess::with('personal_intelisis')
                                            ->orderBy('created_at',$order_by)
                                            ->Paginate($limit);
        }else{
            $list = DmirhIncidentProcess::with('personal_intelisis')->where('rfc_generated',auth()->user()->personal_intelisis->rfc)
                                    ->orderBy('created_at',$order_by)
                                    ->Paginate($limit);
        }

        
        $list->setPath('/rh/process-incident/list');

        if($list != null){
            return ["success" => 1, "data" => $list];
        }else{
            return ["success" => 0,"message" => "No hay datos", "data" => []];
        }
        


    }

    public function addIncidentProcess(Request $request){
        set_time_limit(1000);

        // Se valida que no exista un reporte activo con los mismo parametros y que este activo
        $validate = $this->validateExistsIncidentProcessActive($request);
        $mails_boss=[];
        $full_name_boss=[];

        if($validate['success'] == 1){
            
            try{
                $incident_processeAsistenciaController = new ReporteAsistenciaController();
    
                $aux_request = new Request();
                $aux_request->setMethod('POST');
                $aux_request->query->add($request->input());
                $asistencias = $incident_processeAsistenciaController->getReporteAsistencia($aux_request)->getData(false);

            }catch(\Exception $exc){
                return ["success" => 0, "message" => "Error al obtener las asistencias. ".$exc->getMessage(), "data" => []];
            }
            
            if($asistencias != null || sizeof($asistencias) > 0){

                $analysis_assistence = $this->analyzingReportsAssistance($asistencias);

                $dates = explode(",",$request->fechas);
    
                $incident_process = new DmirhIncidentProcess();
                $incident_process->start_date = $dates[0];
                $incident_process->end_date = $dates[1];
                $incident_process->locations = strtolower($request->ubications);
                $incident_process->payment_period = $request->payment_period;
                $incident_process->status = "activo";
                $incident_process->rfc_generated = auth()->user()->personal_intelisis->rfc;
                $incident_process->collaborators_contemplated_rfc = json_encode($this->list_collaborator_rfc);
                $incident_process->save();
    
                if($incident_process != null){
                    
                    if(sizeof($analysis_assistence) > 0){
                        foreach ($analysis_assistence as $key => $asistencia) {
    
                            $aux_date = Carbon::createFromFormat('d/m/Y', $asistencia->fecha);
                            $date_format = $aux_date->format('Y-m-d');
                            
                            $incident_process_detail = new DmirhIncidentProcessDetail();
                            $incident_process_detail->dmirh_incident_process_id = $incident_process->id;
                            $incident_process_detail->rfc = $asistencia->rfc;
                            $incident_process_detail->name = $asistencia->name;
                            $incident_process_detail->payment_period = $asistencia->payment_period;
                            $incident_process_detail->horario_base = $asistencia->horariobase;
                            $incident_process_detail->entry_hour = $asistencia->entrada;
                            $incident_process_detail->date_incident = $date_format;
                            $incident_process_detail->status = $asistencia->entrada == "--" ? 'Falta injustificada' : $asistencia->estatus;
                            $incident_process_detail->check_registers = json_encode([
                                                                "c1" => $asistencia->c1,
                                                                "c2" => $asistencia->c2,
                                                                "c3" => $asistencia->c3,
                                                                "c4" => $asistencia->c4,
                                                                "c5" => $asistencia->c5,
                                                                "c6" => $asistencia->c6,
                                                                "c7" => $asistencia->c7,
                                                            ]);
                            $incident_process_detail->save();
                        }                

                    }else{
                        $incident_process->observations = "- Sin incidencias encontradas en el perído seleccionado.";
                        $incident_process->save();
                    }
                }
    
                return ["success" => 1, "message" => "", "data" => $incident_process];
    
            }else{
                return ["success" => 0, "message" => "Sin registros de checador", "data" => []];
            }

        }else{
            return ["success" => 0, "message" => $validate['message'], "data" => []];
        }

        

    }

    public function validateExistsIncidentProcessActive($_request){
        
        $active_report = DmirhIncidentProcess::where('payment_period',$_request->payment_period)
        ->where('status','activo')->get();

        if(sizeof($active_report) > 0){
            $array_locations = explode(',',strtolower($_request->ubications));
            
            foreach ($active_report as $incident_process) {
                $locations_report = explode(',', $incident_process->locations);
    
                if (count(array_intersect($array_locations, $locations_report)) > 0) {
                    return ["success" => 0, 'data' => [], "message" => 'Ya existe un reporte para alguna de estas ubicaciones.'];
                }
            }

            return ["success" => 1, 'data' => [], "message" => "No existe reporte con alguna de esas ubicaciones"];
        }else{
            return ["success" => 1, 'data' =>[], "message" => ""];
        }

        return ($active_report);


    }

    public function analyzingReportsAssistance($_reports_assistance){

        /* 
            Se analizan todas las checadas de los usuarios, en donde se descartan aquellas que sean 
            jornada completa, permisos, vacaciones o incapacidades
        */

        $incidents = [];
        $this->getHolidays();
        $this->movements_per_process = $this->analyzingIncidentsWithERP();
        $this->list_collaborator_rfc=[];
        $user_to_exclude = $this->getUserToExclude();

        foreach ($_reports_assistance as $key => $assistence) { 

            //Se valida si el usuario se excluye 
            if(in_array($assistence->rfc,$user_to_exclude) == true){
                continue;
            }

            if(in_array($assistence->rfc,$this->list_collaborator_rfc) == false){
                $this->list_collaborator_rfc[]=$assistence->rfc;
            }

            $validate=false;

            // - Los días de puntualidad, Justificados se descartan de INTRANET
            // - Los días de Vacaciones, Permisos, Incapacidades y 

            if($assistence->estatus == "Puntualidad" || $assistence->estatus == "Justificado"){
                continue;
            }

            //Días no laborables se descartan de INTELISIS
            if($this->holidays['success'] == 1){
                $date_format = Carbon::createFromFormat('d/m/Y', $assistence->fecha);
                if(in_array($date_format->format('Y-m-d'),$this->holidays['data'])){
                    continue;
                }
            }

            // Si hay una falta en donde no tenga checada, se revisa si tiene una incidencia en el ERP y para justificarla (Permiso, vacaciones o incapacidad)
            if($assistence->entrada == "--"){
                // Validar la fecha
                $validate = $this->validateIncidentWithERP($assistence);
                $this->validar_faltas_y_movimientos[$assistence->rfc][] =["fecha"=>$assistence->fecha,
                                                                        "result_analisis_ERP" => $validate,
                                                                        "nombre" => $assistence->name];

                if($validate != null){
                    continue;
                }
            }


            $incidents[] = $assistence;

        }

        return $incidents;
        
    }

    public function getUserToExclude(){
        $settings = DmiControlUserSettings::where('module','user_settings')
                                            ->where('key','incident_process-exclude_user-rfc')
                                            ->where('data',1)
											->get()->pluck('value')->toArray();
        return $settings;
    }

    public function getGerenteOrDirector($_usuario_ad){

        
		$gerente = $this->GeneralFunctionsRepository->getGerente($_usuario_ad);

		$higher_notify = null;

		if($gerente != null){			
			$higher_notify = $gerente;
		}else{
			$director = $gerente = $this->GeneralFunctionsRepository->getDirector($_usuario_ad);
			if($director != null){
				$higher_notify = $director;
			}else{
				return null;
			}
		}

		// Valido si de los resultados es uno de los licenciados, le paso la notificación a personal que pueda justificar por ellos
		$signature_behalf = DmiControlSignaturesBehalf::where('usuario_ad',$higher_notify->usuario_ad)->first();

		if($signature_behalf != null){
			$behalf_usuario_ad = PersonalIntelisis::where("usuario_ad",$signature_behalf->behalf_usuario_ad)->where('status','alta')->first();
			return $behalf_usuario_ad;
		}else{
			return $higher_notify;
		}
    }

    public function singleIncidentProcess(Request $request){

        if(isset($request->incident_process_id) && $request->incident_process_id > 0){
            $status = isset($request->type_incident) ? $request->type_incident : "";

            $single = DmirhIncidentProcess::with(['personal_intelisis'])->where('id',$request->incident_process_id)->first();
            if($single != null){
                $details = DmirhIncidentProcessDetail::where('dmirh_incident_process_id',$single->id)
                                                        ->where('status','like',"%$status%")
                                                        ->get();
                $single->incident_process_detail = $details;
            }

            return ['success' => 1,'data' => $single,'message' => ""];

        }else{
            return ['success' => 0, 'message' => "No se encontro el registro."];
        }

    }

    public function closeIncidentProcess($_incident_process_id){

        set_time_limit(1000);


        $incident_process = DmirhIncidentProcess::with('incident_process_detail')
                                            ->where('status','activo')
                                            ->where('id',$_incident_process_id)->first();


        if($incident_process != null){
            try {
                $this->lastIncidentValidationERP($incident_process);

                $incident_process = DmirhIncidentProcess::with('incident_process_detail')
                                            ->where('status','activo')
                                            ->where('id',$_incident_process_id)->first();


                $this->analyzingCloseProcessIncident($incident_process);

                $now = Carbon::now()->format('d-m-Y H:i:s');
                $incident_process->status = 'cerrado';
                $incident_process->observations = "- Cerrado el reporte el día $now. ".$incident_process->observations;
                $incident_process->save();

                $this->cancelRequests($incident_process);

                return ['success' => 1,'data' => $incident_process,'message' => "Se ha cerrado con éxito."];

            } catch (\Exception $exception) {
                return ['success' => 0,'data' =>[],'message' => "Ha ocurrido un error al cerrar el proceso. ->".$exception->getMessage()];
            }
            

            
        }else{
            return ['success' => 0,'data' =>[],'message' => "No se encontró el proceso."];
        }
    
    }

    public function lastIncidentValidationERP($_incident_process){
		
		$this->getHolidays();
		$this->movements_per_process = $this->analyzingIncidentsWithERP();

		foreach ($_incident_process->incident_process_detail as $key => $incident) {

			if($incident->dmirh_personal_justification_id == null && $incident->entry_hour == '--'){
				$exists_justification = DmirhPersonalJustification::join('personal_intelisis as pi','dmirh_personal_justification.user','=','pi.usuario_ad')
															->where('pi.rfc',$incident->rfc)
															->where('dmirh_personal_justification.date',$incident->date_incident)
															->where('dmirh_personal_justification.status',1)
															->whereNotNull('dmirh_personal_justification.approved_by')
															->select('dmirh_personal_justification.*')
															->first();
	
				// Si ya fue justificado
				if($exists_justification != null){
                    $incident->dmirh_personal_justification_id = $exists_justification->id;
					$incident->observations = "Justificado por ".$exists_justification->approved_by.' desde módulo Autorizar Justificación.';
					//$incident->save();
					continue;
				}

				//Días no laborables se descartan de INTELISIS
				if($this->holidays['success'] == 1){
					if(in_array($incident->date_incident,$this->holidays['data'])){
						continue;
					}
				}

            	$incident->fecha = Carbon::parse($incident->date_incident)->format('d/m/Y');
				// Se valida que no este autorizado en el ERP un permiso, vacacion o incapacidad
				$validate = $this->validateIncidentWithERP($incident);
                // Si se encontro en el ERP
				if($validate != null){
                    unset($incident->fecha);
                    $incident->discard_reason ="Mov: ".$validate->Mov."   mov_intelisis: ".$validate->mov_intelisis;
					$incident->observations = "Movimiento encontrado en Intelisis.";
					$incident->save();
                    continue;
                }

				return $incident;
			}
		}
		
	}

    public function cancelIncidentProcess($_incident_process_id){
        $incident_process = DmirhIncidentProcess::with('incident_process_detail')->where('id',$_incident_process_id)->first();
        
        if($incident_process != null){

            $incident_detail_not_justified = DmirhIncidentProcessDetail::where('dmirh_incident_process_id',$_incident_process_id)
                                                            ->whereNull('dmirh_personal_justification_id')
                                                            ->get();
            $now = Carbon::now()->format('d-m-Y H:is');
            $incident_process->status = 'cancelado';
            $incident_process->observations = "- Cancelado el proceso el día $now. ".$incident_process->observations;
            $incident_process->save();

            /* $details = DmirhIncidentProcessDetail::where('dmirh_incident_process_id',$_incident_process_id)->delete(); */
            $suspentions = DmirhIncidentProcessSuspention::where('dmirh_incident_process_id',$_incident_process_id)->delete();

            return ['success' => 1,'data' => $incident_process,'message' => "Se ha cancelado con éxito."];
        }else{
            return ['success' => 0,'data' => "",'message' => "No se encontró el registro."];
        }
    
    }

    public function listValidateIncident(Request $request){
            
        try {

            $status = isset($request->type_incident) ? $request->type_incident : "";
            //$users = PersonalIntelisis::where('top_plaza_id',auth()->user()->personal_intelisis->plaza_id)->where('status','alta')->get()->pluck('rfc')->toArray();
            $profile = PersonalIntelisis::with('commanding_staff')->where("plaza_id",auth()->user()->personal_intelisis->plaza_id)->where('status','ALTA')->first();
            
            if(sizeof($profile->commanding_staff) > 0){
                $this->get_staff($profile);
            }
            
            $list = DmirhIncidentProcessDetail::with('personal_justification')->join('dmirh_incident_process','dmirh_incident_process.id','=','dmirh_incident_process_detail.dmirh_incident_process_id')
                                        ->where('dmirh_incident_process.status','activo')
                                        ->whereIn('dmirh_incident_process_detail.rfc',$this->staff_personal_intelisis_plaza_id)
                                        ->where('dmirh_incident_process_detail.status','like',"%$status%")
                                        ->select('dmirh_incident_process_detail.*',
                                                'dmirh_incident_process.start_date as incident_process_start_date',
                                                'dmirh_incident_process.end_date as incident_process_end_date')
                                        ->get();
            return ["success" => 1, "data" => $list];

        } catch (\Exception $exc) {
            return ["success" => 0,'message' => "Error al obtener los registros. ".$exc->getMessage()];
        }
        
    }

    public function get_staff($_staff){

        foreach ($_staff->commanding_staff as $key => $staff) {
            if(sizeof($staff->commanding_staff) > 0){
				if($staff->rfc != null){
					$this->staff_personal_intelisis_plaza_id[] = $staff->rfc;
				}
                $this->get_staff($staff);

            }else{
				if($staff->rfc != null){
					$this->staff_personal_intelisis_plaza_id[] = $staff->rfc;
				}
            }
        }
    }

    public function validateCanJustify(){
        // Se valida si aun puede justificar, ya que solo se pueden justificar 2 tolerancias dependiendo el caso, quincenal o semanal
    }
    
    public function addJustificationIncident(Request $request){
        //return $request->input();

        $data= $this->validate(request(),[
      
            'rfc' => 'required', 
            'description' => 'required', 
            'date_incident' => 'required', 
            'type_justification_id' => 'required', 
            'incident_process_id' => 'required', 
            ]);

        try{
            $exists_patch=false;
            $collaborator = PersonalIntelisis::where('rfc',$request->rfc)->where('status','ALTA')->first();

            if($collaborator != null){

                if($request->file('file_justification')){
                    $exists_patch=false;
                    $uploaded_file = $request->file('file_justification');
                    $file_name = $uploaded_file->getClientOriginalName();
          
                    if( Storage::disk("Justifications")->putFileAs("/", $uploaded_file, $file_name)){
                        $exists_patch = true;
                    }
                }

                $justification= new DmirhPersonalJustification();
                $justification->description= "Proceso de incidencia - ".$request->description;
                $justification->type_id= $request->type_justification_id;
                $justification->user= $collaborator->usuario_ad;
                $justification->date= $request->date_incident;
                $justification->approved_by= auth()->user()->usuario;
                $justification->status= 1;
                $justification->file= $exists_patch == true ? $file_name : null;
                $justification->save();

                if($justification != null){
                    $incident = DmirhIncidentProcessDetail::where('id',$request->incident_process_id)->first();

                    $incident->dmirh_personal_justification_id = $justification->id;
                    $incident->save();
                }

                return ["success" => 1, "data" => $incident, "message" => ""];

            }else{
                return ["success" => 0, "data" => null, "message" => "No se encontro al colaboraro a justificar"];
            }

        }catch(\Exception $exc){
            return ["success" => 0, "data" => null, "message" => "No se pudo guardar la justificación"];
        }    
    }

    public function analyzingCloseProcessIncident($_process_incident){

        $final_report = [];

        // Se revisan todas las incidencias que no fueron justificadas

        $incidents_detail = $_process_incident->incident_process_detail;
        $final_report['calendar_month']= Carbon::parse($_process_incident->start_date)->format('m');
        $final_report['calendar_year']= Carbon::parse($_process_incident->start_date)->format('Y');
        $final_report['incident_process']=[
            "incident_process_id" => $_process_incident->id,
            "start_date" => Carbon::parse($_process_incident->start_date)->format('d-m-Y'),
            "end_date" => Carbon::parse($_process_incident->end_date)->format('d-m-Y'),
            "locations" => $_process_incident->locations,
            "payment_period" => $_process_incident->payment_period,
            "status" => $_process_incident->status,
            "rfc_generated" => $_process_incident->rfc_generated,
        ];
    
        forEach($incidents_detail as $key => $incident){

            $es_falta=false;
            if($incident->dmirh_personal_justification_id == null && $incident->discard_reason == null){

                // Para faltas y bono de puntualidad y bono de asistencia
                if(($incident->entry_hour == "" || $incident->entry_hour == null || $incident->entry_hour == "--") || $incident->status == "Suspensión" || $incident->status == "Falta injustificada"){

                    //Se registran los días en los que tiene faltas y que no se presentaron
                    if($incident->entry_hour == "--"){
                        $final_report['faltas'][$incident->rfc][]=$incident->date_incident;
                        $es_falta=true;
                    }

                    if($es_falta == false){
                        $final_report['suspenciones'][$incident->rfc]['cant'] = isset($final_report['suspenciones'][$incident->rfc]['cant']) ? intval($final_report['suspenciones'][$incident->rfc]['cant']) + 1 : 1;  
                        $final_report['suspenciones'][$incident->rfc]['days'] = isset($final_report['suspenciones'][$incident->rfc]['days']) ? ($final_report['suspenciones'][$incident->rfc]['days']).' -'.Carbon::parse($incident->date_incident)->format('d') : '-'.Carbon::parse($incident->date_incident)->format('d');  
                    }
                    
                    $incidents_previous= isset($final_report['bono_asistencia'][$incident->rfc]) ? $final_report['bono_asistencia'][$incident->rfc] : "";
                    $final_report['bono_asistencia'][$incident->rfc]= $incidents_previous." - ".Carbon::parse($incident->date_incident)->format('d');


                    $final_report['bono_puntualidad'][$incident->rfc][]=Carbon::parse($incident->date_incident)->format('d-m-Y');
                }

                // Para bono de puntualidad
                if($incident->status == "Tolerancia" || $incident->status == "Retardo" ){
                    $final_report['bono_puntualidad'][$incident->rfc][]=Carbon::parse($incident->date_incident)->format('d-m-Y');

                    if($incident->status == "Retardo"){
                        $final_report['retardos'][$incident->rfc] = isset($final_report['retardos'][$incident->rfc]) ? intval($final_report['retardos'][$incident->rfc]) + 1 : 1;  
                    }
                }
            }
        }

        // Se obtinene los retardos en el mes, de las persona que tuvieron retardos, pra validar si son acreedores de una suspención
        $final_report['suspenciones_x_retardo_1']=[];
        $final_report['suspenciones_x_retardo_2']=[];

        if(isset($final_report['retardos'])){
            forEach($final_report['retardos'] as $rfc => $cont_retardo){

                $days_suspention=0;
                $number_delays=0;
    
                $retardos_totales_mes = DmirhIncidentProcessDetail::where('rfc',$rfc)
                                                        ->where('status','Retardo')
                                                        ->whereMonth('date_incident',$final_report['calendar_month'])
                                                        ->count();
                $final_report['retardos_totales_mes'][$rfc] = $retardos_totales_mes;
    
                if($retardos_totales_mes >= 5){
                    $suspention = DmirhIncidentProcessSuspention::where('rfc',$rfc)
                                                                    ->where('calendar_month',$final_report['calendar_month'])
                                                                    ->where('calendar_year',$final_report['calendar_year'])
                                                                    ->get();
    
                    if(sizeof($suspention) == 0){
    
                        //Aplica un día de suspención
                        if($retardos_totales_mes >= 5 && $retardos_totales_mes <= 8){
                            $final_report['suspenciones_x_retardo_1'][$rfc] = $retardos_totales_mes;
                            $days_suspention=1;
                        }
    
                        //Aplica 2 días de suspención
                        if($retardos_totales_mes > 8){
                            $final_report['suspenciones_x_retardo_2'][$rfc] = $retardos_totales_mes;
                            $days_suspention=2;
                        }
    
                        $add_suspension = new DmirhIncidentProcessSuspention();
                        $add_suspension->dmirh_incident_process_id = $final_report['incident_process']['incident_process_id'];
                        $add_suspension->rfc = $rfc;
                        $add_suspension->calendar_month = $final_report['calendar_month'];
                        $add_suspension->calendar_year = $final_report['calendar_year'];
                        $add_suspension->days_suspention = $days_suspention;
                        $add_suspension->number_delays = $retardos_totales_mes;
                        $add_suspension->save();
    
                    }else{
    
                        $suma_days_suspention = $suspention->sum('days_suspention');
                        $suma_number_delays = $suspention->sum('number_delays');
    
                        //Aplica un día de suspención
                        if($retardos_totales_mes >= 5 && $retardos_totales_mes <= 8){
                            $dias_a_suspender = 1 - $suma_days_suspention;
                        }
    
                        //Aplica 2 días de suspención
                        if($retardos_totales_mes > 8){
                            $dias_a_suspender = 2 - $suma_days_suspention;
                        }
    
                        if($dias_a_suspender > 0){
                            $add_suspension = new DmirhIncidentProcessSuspention();
                            $add_suspension->rfc = $rfc;
                            $add_suspension->calendar_month = $final_report['calendar_month'];
                            $add_suspension->calendar_year = $final_report['calendar_year'];
                            $add_suspension->days_suspention = $dias_a_suspender;
                            $add_suspension->number_delays = $retardos_totales_mes;
                            $add_suspension->save();
                        }
    
                    }
                }
                
                
                
            }
        }

        $this->insertIncidentsERP($final_report);

        //return $final_report;
        //return $_process_incident->incident_process_detail;
    }

    public function analyzeJustifiedIncident($_incident){
        
        // Se revisa si se justifico en el módulo de justificaciones


    }

    public function insertIncidentsERP($_final_report){
        $result = [];

        // Se obtinene la FALTAS por usuario para crear los movimientos para ERP
        if(isset($_final_report['faltas'])){
            foreach($_final_report['faltas'] as $rfc => $faltas){
                $collaborador = PersonalIntelisis::where('rfc',$rfc)->where('status','alta')->first();
    
                if($collaborador != null){
                    $item = [
                        'tipo_incidencia' => 'F',
                        'personal' => $collaborador->personal_id, 
                        'fecha_inicial' => Carbon::parse($faltas[0])->format('Y-m-d'), // Se obtiene la primera fecha que se tuvo la falta 
                        "cantidad" => sizeof($faltas),
                        'sucursal_trabajo' => $collaborador->branch_code, 
                        'empresa' => $collaborador->company_code, 
                        'referencia' => $collaborador->full_name, 
                        'observaciones' =>"Reporte de ".$_final_report['incident_process']['start_date'].' al '.$_final_report['incident_process']['end_date'], 
                        'concepto' => "Falta Injustificada", 
                        "rfc" => $rfc,
                    ];
                    
                    $result['faltas_erp_movs'][] = $item;
    
                    //$new_incidence = $this->IntelisisRepository->insertIncidencePruebas6000($item);
    
                    $record = new DmirhIncidentProcessMovERP();
                    $record->dmirh_incident_process_id = $_final_report['incident_process']['incident_process_id'];
                    $record->insert_mov_erp = json_encode($item);
                    //$record->mov_intelisis = $new_incidence;
                    $record->observations = "Días: ".implode(", ",$faltas).'  => Por Faltas';
                    $record->save();
                }
            }
        }
        
        /* 
            Se obtiene las SUSPENCIONES generadas por los RETARDOS, 
            las cuales se registran como faltas, los cuales ya siendo >= 5 se convierte en falta y >= 8 se convierte en 2 faltas
        */
        if(isset($_final_report['suspenciones_x_retardo_1'])){
            foreach($_final_report['suspenciones_x_retardo_1'] as $rfc => $suspenciones){
                $collaborador = PersonalIntelisis::where('rfc',$rfc)->where('status','alta')->first();
    
                if($collaborador != null){
    
                    $item=[
                        'tipo_incidencia' => 'F',
                        'personal' => $collaborador->personal_id, 
                        'fecha_inicial' => Carbon::parse($_final_report['incident_process']['start_date'])->format('Y-m-d'), // Son varias fechas, por ende mejor se agrega la fecha inicial del proceso
                        "cantidad" => $suspenciones,
                        'sucursal_trabajo' => $collaborador->branch_code, 
                        'empresa' => $collaborador->company_code, 
                        'referencia' => $collaborador->full_name, 
                        'observaciones' =>"Reporte de ".$_final_report['incident_process']['start_date'].' al '.$_final_report['incident_process']['end_date'].', por '.$suspenciones.' retardos acumulados.', 
                        'concepto' => "Falta Injustificada", 
                        "rfc" => $rfc,
                    ];
                    $result['faltas_erp_movs'][] = $item;
                    //$new_incidence = $this->IntelisisRepository->insertIncidencePruebas6000($item);
    
                    $record = new DmirhIncidentProcessMovERP();
                    $record->dmirh_incident_process_id = $_final_report['incident_process']['incident_process_id'];
                    //$record->mov_intelisis = $new_incidence;
                    $record->insert_mov_erp = json_encode($item);
                    $record->observations = $suspenciones." suspenciones por retardos acumulados";
                    $record->save();
                }
            }
        }     

        if(isset($_final_report['suspenciones_x_retardo_2'])){
            foreach($_final_report['suspenciones_x_retardo_2'] as $rfc => $suspenciones){
                $collaborador = PersonalIntelisis::where('rfc',$rfc)->where('status','alta')->first();
    
                if($collaborador != null){
    
                    $item=[
                        'tipo_incidencia' => 'F',
                        'personal' => $collaborador->personal_id, 
                        'fecha_inicial' => Carbon::parse($_final_report['incident_process']['start_date'])->format('Y-m-d'), // Son varias fechas, por ende mejor se agrega la fecha inicial del proceso
                        "cantidad" => $suspenciones,
                        'sucursal_trabajo' => $collaborador->branch_code, 
                        'empresa' => $collaborador->company_code, 
                        'referencia' => $collaborador->full_name, 
                        'observaciones' =>"Reporte de ".$_final_report['incident_process']['start_date'].' al '.$_final_report['incident_process']['end_date'].', por '.$suspenciones.' retardos acumulados.', 
                        'concepto' => "Falta Injustificada", 
                        "rfc" => $rfc,
                    ];
                    $result['faltas_erp_movs'][] = $item;
                    //$new_incidence = $this->IntelisisRepository->insertIncidencePruebas6000($item);
    
                    $record = new DmirhIncidentProcessMovERP();
                    $record->dmirh_incident_process_id = $_final_report['incident_process']['incident_process_id'];
                    //$record->mov_intelisis = $new_incidence;
                    $record->insert_mov_erp = json_encode($item);
                    $record->observations = $suspenciones." suspenciones por retardos acumulados";
                    $record->save();
                }
            }
        }

        // Se registran las SUSPENCIONES que no fueron justificadas, y las cuales se registraran como faltas.
        if(isset($_final_report['suspenciones'])){
            foreach($_final_report['suspenciones'] as $rfc => $suspencion){
                $collaborador = PersonalIntelisis::where('rfc',$rfc)->where('status','alta')->first();
    
                if($collaborador != null){
    
                    $item = [
                        'tipo_incidencia' => 'F',
                        'personal' => $collaborador->personal_id, 
                        'fecha_inicial' => Carbon::parse($_final_report['incident_process']['start_date'])->format('Y-m-d'), // Son varias fechas, por ende mejor se agrega la fecha inicial del proceso
                        "cantidad" => $suspencion['cant'],
                        'sucursal_trabajo' => $collaborador->branch_code, 
                        'empresa' => $collaborador->company_code, 
                        'referencia' => $collaborador->full_name, 
                        'observaciones' =>"Reporte de ".$_final_report['incident_process']['start_date'].' al '.$_final_report['incident_process']['end_date'].', por suspenciones no justificadas.', 
                        'concepto' => "Falta Injustificada", 
                        "rfc" => $rfc,
                    ];
    
                    $result['faltas_erp_movs'][] = $item;
                    //$new_incidence = $this->IntelisisRepository->insertIncidencePruebas6000($item);
    
                    $record = new DmirhIncidentProcessMovERP();
                    $record->dmirh_incident_process_id = $_final_report['incident_process']['incident_process_id'];
                    //$record->mov_intelisis = $new_incidence;
                    $record->insert_mov_erp = json_encode($item);
                    $record->observations = "Días: ".$suspencion['days'].'  => Por suspenciones';
                    $record->save();
                }
            }
    
        }
        
        // Se obtinene los RETARDOS o TOLERANCIAS por usuario para crear los movimientos para ERP
        if(isset($_final_report['bono_puntualidad'])){
            foreach($_final_report['bono_puntualidad'] as $rfc => $incidentes){
                $collaborador = PersonalIntelisis::where('rfc',$rfc)->where('status','alta')->first();
    
                if($collaborador != null){
                    $item= [
                        'tipo_incidencia' => 'R',
                        'personal' => $collaborador->personal_id, 
                        'fecha_inicial' => Carbon::parse($incidentes[0])->format('Y-m-d'), // Se obtiene la primera fecha que se tuvo la falta 
                        "cantidad" => sizeof($incidentes),
                        'sucursal_trabajo' => $collaborador->branch_code, 
                        'empresa' => $collaborador->company_code, 
                        'referencia' => $collaborador->full_name, 
                        'observaciones' =>"Reporte de ".$_final_report['incident_process']['start_date'].' al '.$_final_report['incident_process']['end_date'], 
                        'concepto' => "Retardos", 
                        "rfc" => $rfc,
                    ];
    
                    $result['bono_puntualidad_erp_movs'][] = $item;
                    //$new_incidence = $this->IntelisisRepository->insertIncidencePruebas6000($item);
    
                    $record = new DmirhIncidentProcessMovERP();
                    $record->dmirh_incident_process_id = $_final_report['incident_process']['incident_process_id'];
                    //$record->mov_intelisis = $new_incidence;
                    $record->insert_mov_erp = json_encode($item);
                    $record->observations = "Días: ".implode(", ",$incidentes).'  => Para bono de puntualidad';
                    $record->save();
                }
            }
        }

        $result['_final_report'] = $_final_report;
        return $result;

    }

    public function getHolidays(){
        $holidays = $this->IntelisisRepository->getHolidays();

        if($holidays != null){

            $holidays_date = array_map(function($item){
                return Carbon::parse($item->Fecha)->format('Y-m-d');
            },$holidays);

            $this->holidays= ["success" => 1, "data" => $holidays_date];
        }else{
            $this->holidays=["success" => 0,"data"=>[], "message" => "No existen días festivos."];
        }
    }

    public function analyzingIncidentsWithERP(){
        /* Se analiza las faltas que tenga el colaborador y se revisa si tiene algun permiso, vacación o incapacidad en ERP y poderle quitar la 
        incidencia de falta */

        $movements_to_processed = $this->IntelisisRepository->getMovementsToProcessed();
        
        if($movements_to_processed['success'] == 1){
            $movements = $movements_to_processed['data'];
            if(sizeof($movements) > 0){
                foreach($movements as $key => $movement){
                    $movement->Personal;
                    $movement->working_day = $this->getWorkSchedule($movement->Rfc);
                }
                
                return $movements;
            }
        }else{
            $this->movements_per_process = null;
        }
    }

    public function getWorkSchedule($_rfc){
        $personal_intelisis = PersonalIntelisis::with(['dmirh_personal_time'])
                                                ->where("rfc",$_rfc)
                                                ->where('status','ALTA')
                                                ->first();
        
        if($personal_intelisis != null){
            try{
                $working_day = null;
                foreach($personal_intelisis->dmirh_personal_time->dmirh_personal_time_details as $key => $day){
                    $working_day[] = $day->week_day;
                }
    
                return $working_day;
            }catch(\Exception $exception){
                return null;
            }
            
        }else{
            return null;
        }

    }

    public function validateIncidentWithERP($_assistence){

        // Se agrupa los incidentes del colaborador al que se validara la fecha

        $movements_group = collect($this->movements_per_process)->where('Rfc',$_assistence->rfc);

        if(sizeof($movements_group) > 0){
            $aux_date = Carbon::createFromFormat('d/m/Y', $_assistence->fecha);
            $validation_date = $aux_date->format('Y-m-d');

            $is_validate = $this->validarFaltasCubiertas($validation_date,$movements_group);

            return $is_validate;
        }else{
            return false;
        }  
    }

    public function validarFaltasCubiertas($_validation_date, $_movements_group) {
		$fechasCubiertas = [];
        $is_validate = null;
        
        // Recorre cada solicitud por procesar que tiene el colaborador
        foreach ($_movements_group as $movement) {
            
            if($movement->working_day != null){
                $fecha = Carbon::parse($movement->FechaD);
                $jornadaMap = array_fill_keys($movement->working_day, true);
                $diasAgregados=0;

                while ($diasAgregados < (abs((int)$movement->Cantidad))) {
                    // Se valida si la fecha cae en día de la jornada laboral
                    $diaSemana = (int)$fecha->format('N'); // 1 (lunes) - 7 (domingo)
                    
                    if (isset($jornadaMap[$diaSemana])) {
                        // Si el día que sigue es festivo, no se incrementan los días agregados, ya que no cuenta como laborable
                        if(in_array($fecha->format('Y-m-d'),$this->holidays['data'])){
                        }else{
                            $diasAgregados++;
                        }                
                        // Comparo si las fechas son iguales, significa que si tiene un movimiento en proceso en ERP
                        if($_validation_date == Carbon::parse($fecha)->format('Y-m-d')){
                            $is_validate= $movement;
                            break;
                        }
                    }

                    $fecha->modify('+1 day');

                    
                }

                if($is_validate != null){
                    break;
                }
            }else{
                continue;
            }
            
        }

        return $is_validate;
	}

    public function collaboratosContemplated(int $_incident_process_id){

        if($_incident_process_id > 0){
            $incident_process = DmirhIncidentProcess::where('id',$_incident_process_id)->first();

            if($incident_process != null){
                $rfcs = json_decode($incident_process->collaborators_contemplated_rfc);

                $collaborators = PersonalIntelisis::whereIn('rfc',$rfcs)->where('status','alta')->get();

                return ['success' => 1,'data' => $collaborators,"message" =>""];
            }else{
                return ['success' => 0,'data'=>[],"message" =>"Sin registros"];
            }
        }else{
            return ['success' => 0,'data'=>[],"message" =>"El id es incorrecto."];
        }

        
    }

    public function getInsertMovERP($_incident_process_id){
        
        if($_incident_process_id > 0){
            $movs = DmirhIncidentProcessMovERP::where('dmirh_incident_process_id',$_incident_process_id)->get();

            $movs = $movs->map(function($item){
                $item->insert_mov_erp = json_decode($item->insert_mov_erp);
                return $item;
            });

            return ['success' => 1,'data' => $movs,"message" =>""];

        }else{
            
            return ['success' => 0,'data'=>[],"message" =>"El id es incorrecto."];
        }
        

    }

    public function sendNotification($_id){
        set_time_limit(120);
        $mails_boss=[];
        $full_name_boss=[];
        $test_email_name=[];

        $incident_process = DmirhIncidentProcess::where('id',$_id)->first();
        $collaborators = DmirhIncidentProcessDetail::where('dmirh_incident_process_id',$_id)->groupBy('rfc')->select('rfc')->get();
        $gerente_director_not_found=[];
        $email_not_sent=[];
        
        foreach ($collaborators as $key => $collaborator) {
            //$personal_intelisis = PersonalIntelisis::with('immediate_boss')->where('rfc',$collaborator->rfc)->where('status','ALTA')->first();
            $personal_intelisis = PersonalIntelisis::with('get_higher')->where('rfc',$collaborator->rfc)->where('status','ALTA')->first();
            try {

                if($personal_intelisis != null){
                    /********************* */
                    // Se busca al Jefe inmediato
                    $boss = $this->getImmediateBossAvailable($personal_intelisis->get_higher);

                    if($boss != null){

                        $mails_boss[$boss->email][]= $personal_intelisis->full_name;
                        $full_name_boss[$boss->email]= $boss->name." ".$boss->name;
                        
                    }else{
                        // Se almacena el gerente o director del colaborador
                        $higher_notify = $this->getGerenteOrDirector($personal_intelisis->usuario_ad);
                                            
                        if($higher_notify != null){
                            
                            $personal_intelisis_boss = PersonalIntelisis::where('usuario_ad',$higher_notify->usuario_ad)->where('status','ALTA')->first();

                            if($personal_intelisis_boss != null){
                                if($personal_intelisis_boss->email != null){
                                    if(!in_array($personal_intelisis_boss->email,$mails_boss)){

                                        $mails_boss[$personal_intelisis_boss->email][]= $personal_intelisis->full_name;
                                        $full_name_boss[$personal_intelisis_boss->email]= $personal_intelisis_boss->full_name;
                                        
                                    }
                                }
                            }
                        }else{
                            // Se obtiene la coordinadora de RRHH en caso de no encontrar a nadie
                            $coordinadora_RRHH=$this->GeneralFunctionsRepository->getControlAuthorizationSignatures('Coordinadora_RRHH',$personal_intelisis->location);
                            $personal_intelisis_rrhh = PersonalIntelisis::where('plaza_id',$coordinadora_RRHH->plaza_id)->where('status','ALTA')->first();

                            $mails_boss[$personal_intelisis_rrhh->email][]= $personal_intelisis->full_name;
                            $full_name_boss[$personal_intelisis_rrhh->email]= $personal_intelisis_rrhh->full_name;
                        }

                    }

                    /********************* */
                }
                
                
            } catch (\Exception $exception) {
                $gerente_director_not_found[] = $personal_intelisis->full_name;
            }

            
        }

        // Se envian los correos a los gerentes o directores de los colaboradores que tuvieron incidencias
        foreach ($mails_boss as $key => $list_user) {
            try {
                if(env("APP_ENV_IS_PROD") == 0){
                    $email_destinatario = auth()->user()->personal_intelisis->email;
                    //$email_destinatario = "eladio.perez@grupodmi.com.mx";
                }else{
                    $email_destinatario = $key;
                }
    
                $data = [
                    'data' =>[
                        'start_date' => Carbon::parse($incident_process->start_date)->format('d-m-Y'),
                        'end_date' => Carbon::parse($incident_process->end_date)->format('d-m-Y'),
                        'name_boss' => $full_name_boss[$key],
                        'list_user' => implode(', ', $list_user),

                    ],
                    'module' => "process_incident_notification",
                    //'to_email' => "eladio.perez@grupodmi.com.mx", //$email_destinatario
                    'to_email' => $email_destinatario, //$email_destinatario
                ];
        
                $this->sendEmail->sendNotificationMailBossIncidentProcess($data);
            } catch (\Exception $exception) {
                $email_not_sent[] = $full_name_boss[$key];
            }
            
        }

        return response()->json(['success' => 1, "data" =>[ "message" => "Notificaciones enviadas",
                                    "gerente_director_not_found" => $gerente_director_not_found,
                                    "email_not_sent" => $email_not_sent]
                                ]);

        

    }

    public function addMultipleJustification(Request $request){

        $incidents_total= sizeof($request->rows);
        $count_justified=0;
        
        foreach ($request->rows as $key => $incidente) {
            try {
                if($incidente['dmirh_personal_justification_id'] == null){
                    $collaborator = PersonalIntelisis::where('rfc',$incidente['rfc'])->where('status','ALTA')->first();

                    if($collaborator != null){

                        $tipo = DmirhCatTypeJustification::where('description',"Entrada fuera de Horario")->first();

                        $justification= new DmirhPersonalJustification();
                        $justification->description= "Justificado desde un Proceso de incidencia";
                        $justification->type_id= $tipo->id;
                        $justification->user= $collaborator->usuario_ad;
                        $justification->date= $incidente['date_incident'];
                        $justification->approved_by= auth()->user()->usuario;
                        $justification->status= 1;
                        $justification->file= null;
                        $justification->save();

                        if($justification != null){
                            $incident = DmirhIncidentProcessDetail::where('id',$incidente['id'])->first();

                            $incident->dmirh_personal_justification_id = $justification->id;
                            $incident->save();
                        }
                        $count_justified++;

                    }
                }

                

            } catch (\Exception $th) {

            }
        }

        return response()->json(['success' => 1, "data"=> ["incidents_total" => $incidents_total, "count_justified" => $count_justified], "message"=>""]);

              
    }

    public function getCutOffDates($type_payment){
        // Obtener fechas de corte de nómina semanal y quincenal

        try{
            $currentDate = Carbon::now();
            $newDate = $currentDate->subMonths(2);// Se toma información de 6 meses hacia atras
            $start_date = $newDate->format('Y-m').'-01';

            $get_cut_off_dates = CatPaymentSchedule::where('fecha_inicio','>=',$start_date)
                                                            ->where('tipo_pago',$type_payment)
                                                            ->get();

            return response()->json(['success' => 1, 'data' => $get_cut_off_dates, "error" => null]);
        }catch(\Exception $exception){

            return response()->json(['success' => 0, 'data' => null, "error" => $exception->getMessage()]);
        }
        

    }

    public function cancelRequests($_incident){
        // Se cancelan las solicitudes de los usuario tal como Permisos, Vacaciones y Justificaciónes
        $_incident = DmirhIncidentProcess::find(28);

        try {
            
            if($_incident != null){
                //Justificaciones
                $end_date = Carbon::parse($_incident->end_date)->format("Y-m-d");

                $justifications = DmirhPersonalJustification::where('created_at','<=',$end_date)
                                                            ->where('status',1)
                                                            ->whereNull('approved_by')
                                                            ->get();

                if(sizeof($justifications) > 0){
                    // Se cancelan las justificaciones
                    foreach ($justifications as $key => $justification) {
                        $justification->approved_by = "Sistemas";
                        $justification->status = 0;
                        $justification->save();
                    }

                }

                $vacations = DmirhVacation::where('date_request','<=',$end_date)
                                                            ->where('status',"solicitado")
                                                            ->get();
                
                if(sizeof($vacations) > 0){
                    // Se cancelan las justificaciones
                    foreach ($vacations as $key => $vacation) {
                        $vacation->status = "cancelado";
                        $vacation->save();
                    }

                }

                $work_permits = DmirhWorkPermit::where('date_request','<=',$end_date)
                                                            ->where('status',"solicitado")
                                                            ->get();
                
                if(sizeof($work_permits) > 0){
                    // Se cancelan las justificaciones
                    foreach ($work_permits as $key => $work_permit) {
                        $work_permit->status = "cancelado";
                        $work_permit->save();
                    }

                }

                return $justifications;
            }
        } catch (\Exception $th) {
            return null;
        }
    }

    public function getImmediateBossAvailable($_personal_intelisis){

		if($_personal_intelisis->rfc != null){
			return $_personal_intelisis;
		}else{
			if($_personal_intelisis->get_higher != null){
				return $this->getImmediateBossAvailable($_personal_intelisis->get_higher);
			}else{
				return null;
			}
		}
	}
    
}
