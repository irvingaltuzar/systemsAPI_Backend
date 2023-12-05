<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis;
use App\Models\DmiControlPlazaSustitution;
use App\Repositories\IntelisisRepository;
use App\Models\DmiBucketSignature;
use App\Models\DmiControlAuthorizationSignature;
use App\Models\DmiRh\DmirhVacationDaysLaw;
use App\Models\DmiRh\DmirhVacation;
use App\Http\Requests\VacationRequest;
use App\Models\DmiRh\DmirhTempTopSustitution;
use App\Models\DmiRh\DmirhLoadVacationAdvance;
use App\Repositories\GeneralFunctionsRepository;
use App\Models\DmiControlSignaturesBehalfAudit;
use App\Models\DmiControlSignaturesBehalf;
use App\Http\Controllers\GenericFunctionsController;
use App\Models\DmiControlProcedureValidation;
use App\Repositories\ToolsRepository;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;
use App\Services\SendEmailService;
use App\Http\Controllers\NotificationCenterController;
use App\Models\DmiControlProcess;



class VacationController extends Controller
{

	private $sendEmail, $IntelisisRepository, $GeneralFunctionsRepository,$toolRepository;
	private $is_sign_on_behalf=false;

	public function __construct(SendEmailService $sendEmail, IntelisisRepository $IntelisisRepository, GeneralFunctionsRepository $GeneralFunctionsRepository, ToolsRepository $toolRepository, NotificationCenterController $notificationCenter )
	{
        $this->middleware('guest',['only'=>'ShowLogin']);
		$this->sendEmail = $sendEmail;
		$this->IntelisisRepository = $IntelisisRepository;
		$this->GeneralFunctionsRepository = $GeneralFunctionsRepository;
		$this->toolRepository = $toolRepository;
		$this->seg_seccion_id = 11;
		$this->GenericFunctionsController = new GenericFunctionsController();
		$this->notificationCenter = $notificationCenter;
	}

    public function listVacation(Request $request){
		$personal_intelisis_usuario_ad = isset($request->user) ? $request->user : Auth::user()->usuario;

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $vacation= [];
        $where_user_by="";

        if(isset($search) && strlen($search) > 0){
            $vacation = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
							->where('personal_intelisis_usuario_ad', 'like', $personal_intelisis_usuario_ad)
                            ->whereRaw("(date_request like '%$search%'
								or status like '%$search%'
								or id like '%$search%'
                            )")
                            ->orderBy('date_request',$order_by)->Paginate($limit);
        }else{

            $vacation = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
			->where('personal_intelisis_usuario_ad', 'like', $personal_intelisis_usuario_ad)
            ->orderBy('date_request',$order_by)
            ->Paginate($limit);
        }

        $vacation->setPath('/rh/vacation/list');

        return $vacation;


    }

    public function getDataVacationDays(Request $request){

		$personal_intelisis_usuario_ad = $request->personal_intelisis_usuario_ad;

		if($personal_intelisis_usuario_ad == null){
			$personal_intelisis_usuario_ad = PersonalIntelisis::where('usuario_ad',Auth::user()->usuario)
												->where('status','ALTA')
												->select('usuario_ad')->pluck('usuario_ad')
												->first();
		}
		$personal_intelisis = PersonalIntelisis::with(['dmirh_personal_time','immediate_boss'])
												->where("usuario_ad",$personal_intelisis_usuario_ad)
												->where('status','ALTA')
												->first();
        if($personal_intelisis != null){
			$data['personal_intelisis_antiquity_date'] = $personal_intelisis->antiquity_date;
			$data['available_periods'] = $this->getVacationPeriods($personal_intelisis);

			//Información del periodo actual

			return ["success" => 1, "data" => $data];
		}else{
            return ["success" => 0, "message" =>"No ha iniciado sesión el usuario."];
        }

    }

    public function getVacationPeriods($_personal_intelisis){


        $data = [];
        $vacation_period = "";
		$date_current = Carbon::now()->format('Y-m-d');
        $current_year = Carbon::parse($date_current)->format('Y');
		$antiquity_month_day = Carbon::parse($_personal_intelisis->antiquity_date)->format('m-d');

		//**** Periodo Actual */
			$current_period["period_id"] = 1;
			$current_period["type_period"] = "actual";
			// Días tomados registrados en intelisis
			$current_period['intelisis_days_taken'] = $this->IntelisisRepository->getDaysTakenVacation($_personal_intelisis->personal_id);
			$count = 0;
			foreach ($current_period['intelisis_days_taken'] as $key => $item) {
				if(isset($item->Cantidad)){
					$count = intval($item->Cantidad);
				}
			}

			$current_period['intelisis_days_taken_count'] = $count;
			//Diferencias de años trabajados hasta la fecha actual
			$current_period['date_current'] = $date_current;
			$current_period['antiquity_date'] = Carbon::parse($_personal_intelisis->antiquity_date)->format('Y-m-d');
			//dd();
			$current_period['years_worked'] = date_diff(date_create($current_period['date_current']),date_create($current_period['antiquity_date']))->y;

			// Fecha del aniversario actual
			$current_period['current_anniversary']= $current_year.'-'.$antiquity_month_day;

			// Se verifica si se toma la tabla de vacaciones del 2022 o del 2023
			$current_period['which_table_is_taken']="";
			if($current_year >= 2024){
				$current_period['which_table_is_taken']=2023;
			}else{
				if($date_current < $current_period['current_anniversary']){
					// Se toma la tabla del 2022
					$current_period['which_table_is_taken']=2022;
				}else{
					// Se toma la tabla del 2023
					$current_period['which_table_is_taken']=2023;
				}
			}

			// Se verifica que tipo de usuario
			if(strpos(strtoupper($_personal_intelisis->position_company_full),'DIRECTOR') !== false && strtoupper($_personal_intelisis->position_company) == "DIRECTOR"){
				if($current_period['which_table_is_taken'] == 2022){
					$current_period["vacation_type"] = "directors_2022";
				}else{
					$current_period["vacation_type"] = "directors";
				}

			}else{
				if($current_period['which_table_is_taken'] == 2022){
					$current_period["vacation_type"] = "normal_2022";
				}else{
					$current_period["vacation_type"] = "normal";
				}
			}

			//$current_period["vacation_days_law"] = DmirhVacationDaysLaw::where('vacation_type',$current_period["vacation_type"])->where('anniversary',$current_period['years_worked'])->first();
			$current_period["vacation_days_law"] = PersonalIntelisis::where('usuario_ad',$_personal_intelisis->usuario_ad)->where('status','ALTA')->first();

			if($current_period["vacation_days_law"] != null){
				$current_period["available_days"] = $current_period['intelisis_days_taken_count'];
				$current_period["vacation_days_law"]['days'] = $current_period["vacation_days_law"]->total_vacation_days;
			}else{
				//$current_period["available_days"] = 0;
				$current_period["available_days"] = $current_period['intelisis_days_taken_count'];
				$current_period["vacation_days_law"]['days'] = 0;
			}



			if($current_period['date_current'] >= $current_period['current_anniversary']){
				$current_period["current_period_year"] = ($current_year-1)."-".($current_year);
				$current_period["current_period_date"] = ($current_year-1).'-'.$antiquity_month_day." al ".($current_year).'-'.$antiquity_month_day;
			}else{
				$current_period["current_period_year"] = ($current_year-2)."-".($current_year-1);
				$current_period["current_period_date"] = ($current_year-2).'-'.$antiquity_month_day." al ".($current_year-1).'-'.$antiquity_month_day;
			}


		//**** Periodo Proximo a utilizar */
		/* $current_period["period_id"] = 2;
		$current_period["type_period"] = "anticipado"; */



		$data[]=$current_period;
        return $data;

    }

    public function addRecord(VacationRequest $request){

		$temp_request = new Request();
		$temp_request->setMethod("GET");
		$temp_request->query->add([]);
		$have_vacation_request = $this->checkRequestedVacation($temp_request);

		if($have_vacation_request['has_requests'] == false){
			$recordNew = new DmirhVacation();
			$recordNew->personal_intelisis_usuario_ad = $request->personal_intelisis_usuario_ad;
			$recordNew->personal_id = Auth::user()->personal_intelisis->personal_id;
			$recordNew->period = $request->period_selectd['current_period_year'];
			$recordNew->type_period = $request->period_selectd['type_period'];
			$recordNew->start_date = $request->start_date;
			$recordNew->end_date = $request->end_date;
			$recordNew->return_date = $request->return_date;
			$recordNew->total_days = $request->total_days;
			$recordNew->previous_balance = $request->period_selectd['available_days'];
			$recordNew->date_request = Carbon::now();
			$recordNew->document = null;
			$recordNew->mov_intelisis = null;
			$recordNew->status = "solicitado";
			$recordNew->save();

			if($recordNew != null){
				$this->creat_signatures($recordNew);
				$recordComplete = $this->singleRecord($recordNew->id);

				return ["success" => 1, "data"=> $recordComplete];
			}else{
				return ["success" => 0, "message" =>"Se a producido un error al intenta, intentelo de nuevo."];
			}
		}else{
			return ["success" => 2, "message" =>"Actualmente cuenta con un registro de vacaciones en proceso de autorización, espere a que termine o cancele la solicitud."];
		}



    }

    public function creat_signatures($_record_saved){
        $seg_seccion = 11;
        $profile_employee = PersonalIntelisis::where('usuario_ad',$_record_saved->personal_intelisis_usuario_ad)
												->where('status','ALTA')->first();
        $creat_signatures = [];

		//Firma de Jefe Inmediato
		$temp_plaza_immediate_boss = $this->getContolPlazaSubstitution($profile_employee->top_plaza_id);
		$plaza_immediate_boss = $temp_plaza_immediate_boss != null ? $temp_plaza_immediate_boss->substitute_plaza_id : $profile_employee->top_plaza_id;
        $immediate_boss = PersonalIntelisis::where('plaza_id',$plaza_immediate_boss)->where('status','ALTA')->first();
		
		$by_plaza_sustitution = null;
		if($temp_plaza_immediate_boss != null){
			$by_plaza_sustitution = $temp_plaza_immediate_boss;
		}

		$temp_plaza_dpto_rrhh= $this->GeneralFunctionsRepository->getControlAuthorizationSignatures('Coordinadora_RRHH',$profile_employee->location);
		$by_plaza_sustitution_rrhh = null;
		if($temp_plaza_dpto_rrhh->by_plaza_sustitution != null){
			$by_plaza_sustitution_rrhh= $temp_plaza_dpto_rrhh->by_plaza_sustitution;
		}
		$dpto_rrhh = PersonalIntelisis::where('plaza_id',$temp_plaza_dpto_rrhh->plaza_id)->where('status','ALTA')->first();

		// En caso de no tener plaza el personal, se utiliza el usuario_ad que se guardo en el mismo campo
		if($dpto_rrhh == null){
			$dpto_rrhh = PersonalIntelisis::where('usuario_ad',$temp_plaza_dpto_rrhh->plaza_id)->where('status','ALTA')->first();
			$by_plaza_sustitution_rrhh= null;
		}

        $creat_signatures[] =[
            "personal_intelisis_usuario_ad" => $profile_employee->usuario_ad,
            "order" => 1,
            "status" => 'pendiente',
			"is_automatic" => null,
			'by_plaza_sustitution' => null
        ];
        $creat_signatures[] =[
            "personal_intelisis_usuario_ad" => $immediate_boss->usuario_ad,
            "order" => 2,
            "status" => NULL,
			"is_automatic" => null,
			'by_plaza_sustitution' => $by_plaza_sustitution

        ];
        $creat_signatures[] =[
            "personal_intelisis_usuario_ad" => $dpto_rrhh->usuario_ad,
            "order" => 3,
            "status" => 'firmado',
            "signed_date" => Carbon::now(),
			"is_automatic" => 1,
			'by_plaza_sustitution' => $by_plaza_sustitution_rrhh

        ];

        // Se crean las firmas
        if(sizeof($creat_signatures) > 0){
            foreach ($creat_signatures as $key => $sign) {
                $sign_employee = new DmiBucketSignature();
                $sign_employee->origin_record_id = $_record_saved->id;
                $sign_employee->seg_seccion_id = $this->seg_seccion_id;
                $sign_employee->personal_intelisis_usuario_ad = $sign['personal_intelisis_usuario_ad'];
                $sign_employee->status = $sign['status'];
                $sign_employee->signed_date = isset($sign['signed_date']) ? $sign['signed_date'] : null ;
                $sign_employee->order = $sign['order'];
				$sign_employee->exists_signatures_behalves_audit = (isset($sign['by_plaza_sustitution']) && $sign['by_plaza_sustitution'] != null ? 1 : null);
				$sign_employee->is_automatic = $sign['is_automatic'];
                $sign_employee->save();

				if(isset($sign['by_plaza_sustitution']) && $sign['by_plaza_sustitution'] != null){
					$audit_signature = new DmiControlSignaturesBehalfAudit();
					$audit_signature->origin_record_id = $_record_saved->id;
					$audit_signature->seg_seccion_id = $this->seg_seccion_id;
					$audit_signature->sign_behalf_usuario_ad = $sign['personal_intelisis_usuario_ad'];
					$audit_signature->signature_order = $sign['order'];
					$audit_signature->dmi_bucket_signatures_id = $sign_employee->id;
					$audit_signature->origin_table = 'control_plaza_substitution';
					$audit_signature->origin_table_id = $sign['by_plaza_sustitution']->id;
					$audit_signature->save();
				}
            }
        }


    }

	public function getContolPlazaSubstitution($_plaza_id){
		$substitute_plaza = DmiControlPlazaSustitution::where('plaza_id',$_plaza_id)->first();
		return $substitute_plaza;

	}
	public function getControlAuthorizationSignatures(){

		$control_authorization_signatures = DmiControlAuthorizationSignature::join('dmicontrol_process','dmicontrol_authorization_signatures.dmi_control_process_id','=','dmicontrol_process.id')
																->where('dmicontrol_process.name','vacation')
																->select('dmicontrol_authorization_signatures.*')
																->first();
		return $control_authorization_signatures;

	}

	public function signDocument(Request $request){

        $personal_intelisis_usuario_ad = $request->personal_intelisis_usuario_ad;
		$this->seg_seccion_id= 11;
        $sign = DmiBucketSignature::with(['personal_intelisis'])->where('origin_record_id',$request->record_id)
                        ->where('seg_seccion_id',$this->seg_seccion_id)
                        ->where('personal_intelisis_usuario_ad',$personal_intelisis_usuario_ad)
                        ->where('status','pendiente')
                        ->first();


        if($sign != null){
            $status = null;
            if($request->status == true){
                $status = "firmado";
            }else if($request->status == false){
                $status = "rechazado";
            }

            if($status != null){
                $sign->signed_date = Carbon::now();
                $sign->status = $status;
                $sign->save();

				//Solo si es de los señores
				if(isset($request->sign_on_behalf)){
					if($request->sign_on_behalf == true){
						$signature_behalf = DmiControlSignaturesBehalf::where('seg_seccion_id',$this->seg_seccion_id)->where('behalf_usuario_ad',auth()->user()->usuario)->first();

						$audit = new DmiControlSignaturesBehalfAudit();
						$audit->origin_record_id = $request->record_id;
						$audit->seg_seccion_id = $this->seg_seccion_id;
						$audit->signature_order = $sign->order;
						$audit->sign_behalf_usuario_ad = auth()->user()->usuario;
						$audit->origin_table = 'control_signatures_behalves';
						$audit->origin_table_id = $signature_behalf->id;
						$audit->save();
		
						$this->is_sign_on_behalf=true;
					}else{
						$this->is_sign_on_behalf=false;
					}
				}

                if($sign != null){
                    if($status == "firmado"){
                        // Se cambia el estatus a pendiente de la siguiente firma y si es la ultima
                        // Se cambia el estatus al permiso a *autorizado*

                        $signatures = DmiBucketSignature::with(['personal_intelisis'])->where('origin_record_id',$request->record_id)
                                        ->where('seg_seccion_id',$this->seg_seccion_id)
                                        ->get();

                        $end_sign = sizeof($signatures)-1;
                        $active_sign = false;
                        foreach ($signatures as $key => $item) {
                            if($item->status == "firmado"){
                                if($end_sign == $key){
                                    // Se verifica si se completaron todas las firmas y se cambia el estatus al permiso en caso de estar todas las firmas
                                    $verify_signatures = DmiBucketSignature::where('origin_record_id',$request->record_id)
                                        ->where('seg_seccion_id',$this->seg_seccion_id)
                                        ->get();

                                    $cont_sign = 0;
                                    foreach($verify_signatures as $key => $v_item){
                                        if($v_item->status == "firmado" && $v_item->signed_date != null){
                                            $cont_sign++;
                                        }
                                    }

                                    if($cont_sign == sizeof($verify_signatures)){
                                        $record = DmirhVacation::with('personal_intelisis')->where('id',$request->record_id)->first();
                                        $record->status = "autorizado";
                                        $record->save();

										// Se registra las vacaciones en intelisis y se guarda el número de movimiento de intelisis al que pertenece
                                        $insert_incidence = $this->sendVacationIntelisis($record);
										$record->mov_intelisis = $insert_incidence;
										$record->save();

                                        $this->generarPDFRecord($request->record_id);
                                        $this->sendConfirmationEmail($request->record_id);

                                    }

                                }else{
                                    $active_sign = true;
                                }
                            }else if($active_sign == true && $item->status == null){
                                //Activamos la siguiente firma
                                $item->status = "pendiente";
                                $item->save();
                                $active_sign = false;

								//Enviamos la notificación de la firma de vacaciones
								$this->sendEmail->sendNextSigner($item, 'next_signer');

								//Se agrega la notificación para el Front
								$this->notificationCenter->addNotificationCenter(
															$item->personal_intelisis_usuario_ad,
															"Vacaciones",
															"Tiene una solicitud de vacaciones por firmar con folio ".$request->record_id.", consulta para mas detalles...",
															"notification",
															"rh/vacaciones/autorizar-vacaciones",
															"notification",
															"media"
														);
								break;
                            }

                        }

                    }else if($status == "rechazado"){
                        // Se eliminan cancelan las demas firmas
                        $this->rejectedSignature($request->record_id);
						$this->generarPDFRecord($request->record_id);
                    }
                    $record = $this->singleRecord($request->record_id);
                    return ['success' => 1, 'data'=>$record, "action" => $status];
                }
            }else{
                return ['success' => 0, 'message'=> 'El estatus no enviado no es correcto.'];
            }


        }else{
            return ['success' => 0, 'message'=> 'No se encontró la firma solicitada.'];
        }

    }

    public function singleRecord($_id){

        $record = DmirhVacation::with(['personal_intelisis.dmirh_personal_time','personal_intelisis.immediate_boss'])->find($_id);
		if($record != null){
            if($record->status == "solicitado"){
                $temp_request = new Request();
                $temp_request->setMethod("GET");
                $temp_request->query->add(['personal_intelisis_usuario_ad'=>$record->personal_intelisis_usuario_ad]);
				$getDataVacationDays = $this->getDataVacationDays($temp_request);
                if($getDataVacationDays['success'] == 1){
                    $record->data_vacation_days = $getDataVacationDays['data'];
                }else{
                    $record->data_vacation_days = null;
                }

            }else {
				//obtener pediodos
				$personal_intelisis = PersonalIntelisis::where("usuario_ad",$record->personal_intelisis_usuario_ad)
												->where('status','ALTA')
												->first();

				$record->data_vacation_days = $this->getInfoVacationPeriods($record,$personal_intelisis);

            }

            return $record;
        }else{
            return "No se encontro el registro solicitado.";
        }

    }


	public function getInfoVacationPeriods($_vacation,$_personal_intelisis){
		//Para vacaciones con estatus autorizado o cancelado

        $data = [];
        $vacation_period = "";
		$date_current = $_vacation->date_request;
        $current_year = Carbon::parse($date_current)->format('Y');
		$antiquity_month_day = Carbon::parse($_personal_intelisis->antiquity_date)->format('m-d');

		//**** Periodo Actual */
			//Diferencias de años trabajados hasta la fecha actual
			$current_period['date_current'] = $date_current;
			$current_period['antiquity_date'] = Carbon::parse($_personal_intelisis->antiquity_date)->format('Y-m-d');
			$current_period['years_worked'] = date_diff(date_create($current_period['date_current']),date_create($current_period['antiquity_date']))->y;

			// Se verifica que tipo de usuario
			if(strpos(strtoupper($_personal_intelisis->position_company_full),'DIRECTOR') !== false && strtoupper($_personal_intelisis->position_company) == "DIRECTOR"){
				$current_period["vacation_type"] = "directors";
			}else{
				$current_period["vacation_type"] = "normal";
			}
			$current_period["vacation_days_law"] = DmirhVacationDaysLaw::where('vacation_type',$current_period["vacation_type"])
																		->where('anniversary',$current_period['years_worked'])
																		->select('days')
																		->pluck('days')
																		->first();

			return $current_period;

    }

	public function cancelRecord($_id){

        $record_cancel = DmirhVacation::find($_id);
        if($record_cancel != null){

            //Se verifica, si esta autorizado, se manda cancelar tambien hacia intelisis
            if($record_cancel->status == "autorizado"){
                // Aqui se agrega la petición para cancelar en intelisis
                $response_intelisis = $this->cancelarVacationIntelisis($record_cancel->mov_intelisis);

				// Estatus que puede retornar Intelisis
				//  -PROCESADA EN NOMINA
				//  -ERROR AL CANCELAR
				//  -CANCELADA
				//  -ELIMINADA

				if($response_intelisis == "CANCELADA"){
					$record_cancel->status = "cancelado";
					$record_cancel->save();

					$this->generarPDFRecord($record_cancel->id);
					$this->sendConfirmationEmail($record_cancel->id);
					$this->GeneralFunctionsRepository->deleteFile(['url'=>$record_cancel->document]);

					return ['success' => 1, 'message' => "Registro cancelado con éxito."];

				}else{
					return ['success' => 2, 'message' => "No se pudo tramitar la cancelación, contacta con el Coordinador tu de RRHH"];
				}

            }else{
				$record_cancel->status = "cancelado";
            	$record_cancel->save();

				$this->generarPDFRecord($record_cancel->id);
                $this->sendConfirmationEmail($record_cancel->id);

                return ['success' => 1, 'message' => "Registro cancelado con éxito."];
			}

            /* if($record_cancel != null){

            }else{
                return ['success' => 0, 'message' => "El registro no pudo ser eliminado, intente de nuevo."];
			} */

        }else{
            return ['success' => 0, 'message' => "El registro no ha sido encontrado."];
        }


    }

	public function checkRequestedVacation(Request $request){

		$personal_intelisis_usuario_ad = isset($request->user) ? $request->user : Auth::user()->usuario;

		$vacations = DmirhVacation::where('personal_intelisis_usuario_ad',$personal_intelisis_usuario_ad)->where('status','solicitado')->get();
		if(sizeof($vacations) > 0){
			// Tiene una solicitud activa
			return ["success" => 1, "has_requests" => true];
		}else{
			// No tiene solicitud activa
			return ["success" => 1, "has_requests" => false];
		}

	}

	public function generarPDFRecord($_id){

		$year = Carbon::now()->format("Y");
        $data = $this->singleRecord($_id);
		$record = DmirhVacation::where('id',$_id)->first();

		// Se agrega la jornada del usuario
		$workday = $this->GeneralFunctionsRepository->getWorkDay($data->personal_intelisis->usuario_ad);
		$data->workday = $workday;

		//obtener pediodos
		$month_day = Carbon::parse($data->personal_intelisis->antiquity_date)->format('d-m');
		$periods = explode('-',$data->period);
		$period_text = $month_day.'-'.$periods[0].' al '.$month_day.'-'.$periods[1];
		//return view('PDF.DMI.RRHH.vacation')->with(compact('data','period_text'));

		$vacation_days_law= "";
		if(isset($data->data_vacation_days['vacation_days_law'])){
			$vacation_days_law = $data->data_vacation_days['vacation_days_law'];
		}else{
			$vacation_days_law = $data->data_vacation_days['available_periods'][0]['vacation_days_law']->days;
		}

        $pdf = PDF::loadView('PDF.DMI.RRHH.vacation',  [
            'data' => $data,
            'period_text' => $period_text,
			'vacation_days_law' => $vacation_days_law
        ], [], [
            'format' => 'A4',
        ]);

		$path_folder="";

		if($this->is_sign_on_behalf == true){
			$path_folder="storage/Council_Documents/vacation";

			if(!file_exists($path_folder)) {
				mkdir($path_folder, 0700,true);
			}
			$file_name = $data->id.'_'.time().'_vacation.pdf';
			$path = "{$path_folder}/{$file_name}";
			$pdf->save(public_path($path));

			$tmp_path = $path;

			if($pdf != null){
				$record = DmirhVacation::where('id',$_id)->first();
				$record->document = $tmp_path;
				$record->save();
			}

		}
		$file_name = $this->toolRepository->getFilename($_id, $data->personal_intelisis, $data->start_date);

		$path = "{$year}/Vacaciones/{$data->personal_intelisis->company_code}/{$data->personal_intelisis->branch_code}/{$file_name}";

		Storage::disk('IncidenciasIntranet')->put($path, $pdf->output());

		$tmp_path = "storage/IncidenciasIntranet/{$path}";

		$record = DmirhVacation::where('id',$_id)->first();
		$record->document = $tmp_path;
		$record->save();
    }

	public function rejectedSignature($_record_id){
        $cancel_signatures = DmiBucketSignature::where('origin_record_id',$_record_id)
                        ->where('seg_seccion_id',$this->seg_seccion_id)
                        ->get();

        foreach($cancel_signatures as $key => $sign){
            if($sign->signed_date == null){
                $sign->signed_date = Carbon::now();
                $sign->status = "cancelado";
                $sign->save();
            }
        }

        // Y se rechaza el permiso tambien
        $record = DmirhVacation::find($_record_id);
        $record->status = "rechazado";
        $record->save();

        $this->sendConfirmationEmail($record->id);
    }

	public function sendConfirmationEmail($record_id){

        $record = DmirhVacation::with('personal_intelisis')->where('id',$record_id)->first();

        $subject="Permiso de Vacaciones";
        $name=strtoupper($record->personal_intelisis->name.' '.$record->personal_intelisis->last_name);
        $email=$record->personal_intelisis->email;

		$data = [
			'data' =>[
				'id' => $record->id,
				'date' => $record->updated_at,
				'subject' => $subject,
				'name' => $name,
				'status' => strtoupper($record->status),
			],
			'module' => "vacation",
			'to_email' => $email,
		];

		//Se agrega la notificación para el Front
		$type_notification = $record->status == "autorizado" ? "approved_request" : "cancel_request";

		$this->notificationCenter->addNotificationCenter(
									$record->personal_intelisis_usuario_ad,
									"Vacaciones",
									"La solicitud de vacaciones con folio ".$record->id." ha sido $record->status, consulta para mas detalles...",
									"notification",
									"rh/vacaciones/mis-vacaciones",
									$type_notification,
									"media");

		$this->sendEmail->notificationEmailRRHH($data);

    }

	public function authorizeVacationList(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $profile = PersonalIntelisis::where("usuario_ad",Auth::user()->usuario)
									->where('status','ALTA')
									->first();

		// Se verifica si puede firmar en lugar de alguien más
		$check_signature_behalf = $this->GenericFunctionsController->checkUserSignOnBehalf($this->seg_seccion_id,$profile->usuario_ad);

        if(isset($search) && strlen($search) > 0){

            $list = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
                                    ->join('dmi_bucket_signatures as bs','bs.origin_record_id','=','dmirh_vacation.id')
                                    ->where('bs.seg_seccion_id',$this->seg_seccion_id)
                                    ->where('dmirh_vacation.status','solicitado')
                                    ->where('bs.status','pendiente');

									if($check_signature_behalf['is_valid'] == 1){
										$list = $list->whereIn('bs.personal_intelisis_usuario_ad',array_merge($check_signature_behalf['data'],[$profile->usuario_ad]));
									}else{
										$list = $list->where('bs.personal_intelisis_usuario_ad',$profile->usuario_ad);
									}

                                    $list = $list->whereNull('bs.signed_date')
                                    ->where('bs.order', '>=' , 2)
									->where(
										function ($q) use ($search){
											return $q->whereRelation('personal_intelisis',function($q) use ($search){
												return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
											})
											->orWhereRaw("(dmirh_vacation.date_request like '%$search%'
												or dmirh_vacation.status like '%$search%'
												or dmirh_vacation.id like '%$search%'
											)");
										}
									)
                                    ->orderBy('dmirh_vacation.created_at',$order_by)
                                    ->selectRaw('dmirh_vacation.*')
                                    ->Paginate($limit);
        }else{

            $list = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
                                    ->join('dmi_bucket_signatures as bs','bs.origin_record_id','=','dmirh_vacation.id')
                                    ->where('bs.seg_seccion_id',$this->seg_seccion_id)
                                    ->where('dmirh_vacation.status','solicitado')
                                    ->where('bs.status','pendiente');

									if($check_signature_behalf['is_valid'] == 1){
										$list = $list->whereIn('bs.personal_intelisis_usuario_ad',array_merge($check_signature_behalf['data'],[$profile->usuario_ad]));
									}else{
										$list = $list->where('bs.personal_intelisis_usuario_ad',$profile->usuario_ad);
									}

                                    $list = $list->whereNull('bs.signed_date')
                                    ->where('bs.order', '>=' , 2)
                                    ->orderBy('dmirh_vacation.created_at',$order_by)
                                    ->selectRaw('dmirh_vacation.*')
                                    ->Paginate($limit);
        }
        $list->setPath('/rh/vacation/authorize-vacation-list');

        return $list;




    }

    public function staffVacationList(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $staff_personal_intelisis_usuario_ad = [];
        $profile = PersonalIntelisis::with('commanding_staff')->where("usuario_ad",Auth::user()->usuario)->where('status','ALTA')->first();
        $this->staff_personal_intelisis_usuario_ads = [];

		if(sizeof($profile->commanding_staff) > 0){
			$this->get_staff($profile);
		}

        if(isset($search) && strlen($search) > 0){
            $list = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
									->where(
										function ($q) use ($search, $order_by){
											return $q->whereRelation('personal_intelisis',function($q) use ($search){
													return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
												})
												->orWhereRaw("(dmirh_vacation.date_request like '%$search%'
																or dmirh_vacation.status like '%$search%'
																or dmirh_vacation.id like '%$search%'
														)")
												->orderBy('dmirh_vacation.created_at',$order_by);

									})
                                    ->whereIn('personal_intelisis_usuario_ad',$this->staff_personal_intelisis_usuario_ads)
                                    ->selectRaw('dmirh_vacation.*')
                                    ->Paginate($limit);
        }else{

            $list = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
                                    ->whereIn('personal_intelisis_usuario_ad',$this->staff_personal_intelisis_usuario_ads)
                                    ->orderBy('dmirh_vacation.created_at',$order_by)
                                    ->selectRaw('dmirh_vacation.*')
                                    ->Paginate($limit);
        }

        $list->setPath('/rh/vacation/staff-vacation-list');

        return $list;




    }

    public function get_staff($_staff){

        foreach ($_staff->commanding_staff as $key => $staff) {
            if(sizeof($staff->commanding_staff) > 0){
                $this->staff_personal_intelisis_usuario_ads[] = $staff->usuario_ad;
                $this->get_staff($staff);

            }else{
                $this->staff_personal_intelisis_usuario_ads[] = $staff->usuario_ad;
            }
        }
    }

	public function cancelarVacationIntelisis($_mov_intelisis){

		$cancel_intelisis = $this->IntelisisRepository->cancelIncidence($_mov_intelisis);
		return $cancel_intelisis;

    }

	public function generalReport(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $start_date = isset($request->start_date) ? $request->start_date : null;
        $end_date = isset($request->end_date) ? $request->end_date : null;
        $status = isset($request->status) ? $request->status : null;

		//Se verifica que el usuario sea coordinador
		$exist_user_coordinador = DmiControlProcedureValidation::where('key','Vacation_general_report_coordinador_rr_hh-usuario_ad')->where('value',Auth::user()->usuario)->first();

		//Se obtiene la locación del usuario de rrhh para solo mostrar registros de su locacion
		//$location_user_rrhh = PersonalIntelisis::where('usuario_ad',Auth::user()->usuario)->where('status','ALTA')->pluck('location')->first();
		//$users_location= PersonalIntelisis::where('location',$location_user_rrhh)->where('status','ALTA')->whereNotNull('usuario_ad')->pluck('usuario_ad');
		
		// Se obtienen las locaciones a las que esta asiganada la coordinadora
		$get_locations = DmiControlProcess::join("dmicontrol_authorization_signatures","dmicontrol_authorization_signatures.dmi_control_process_id", "=", "dmicontrol_process.id")
											->whereNull("dmicontrol_process.deleted_at")
											->whereNull("dmicontrol_authorization_signatures.deleted_at")
											->where("dmicontrol_authorization_signatures.plaza_id","=",Auth::user()->personal_intelisis->plaza_id)
											->where("dmicontrol_process.name","=","Coordinadora_RRHH")
											->select("dmicontrol_process.*")
											->get()
											->pluck("location")
											->toArray();
		$users_location= PersonalIntelisis::whereIn('location',$get_locations)->where('status','ALTA')->whereNotNull('usuario_ad')->pluck('usuario_ad');				

        if((isset($search) && strlen($search) > 0) || $start_date != null || $end_date != null || $status != null){

            $start_date = isset($request->start_date) ? Carbon::parse($request->start_date)->format('Y-m-d') : '2000-01-01';
            $end_date = isset($request->end_date) ? Carbon::parse($request->end_date)->format('Y-m-d') : (Carbon::now())->format('Y-m-d');
            $status = isset($request->status) ? $request->status : null;

            $list = DmirhVacation::with(['personal_intelisis.dmirh_personal_time'])
									->where(function ($query) use ($search){
										return $query->whereRelation('personal_intelisis',function($query) use($search){
											return $query->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
										})
										->orWhereRaw("(
											dmirh_vacation.id like '%$search%'
											or dmirh_vacation.date_request like '%$search%'
										)");
									})
									/* ->whereRelation('personal_intelisis',function($q) use ($search){
										return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
									}) */
									/* ->orWhereRaw("(dmirh_vacation.status like '%$status%'
										or dmirh_vacation.id like '%$search%'
										or dmirh_vacation.date_request like '%$search%'
                                    )") */;

									if($exist_user_coordinador != null){
										$list = $list->whereIn('personal_intelisis_usuario_ad',$users_location);
									}

                                    $list = $list->whereDate("date_request",">=",$start_date)
                                    ->whereDate("date_request","<=",$end_date)
                                    ->where("status",'like',"%$status%")
                                    ->orderBy('date_request',$order_by)
                                    ->Paginate($limit);
        }else{
            $list = DmirhVacation::with(['personal_intelisis.dmirh_personal_time']);

									if($exist_user_coordinador != null){
										$list = $list->whereIn('personal_intelisis_usuario_ad',$users_location);
									}

									$list = $list->orderBy('date_request',$order_by)
                                    ->Paginate($limit);
        }

        $list->setPath('/rh/vacation/general-report');

        return $list;
    }

	public function sendVacationIntelisis($_record){

		$info['tipo_incidencia']="v";
		$info['personal']=$_record->personal_intelisis->personal_id;
		$info['fecha_inicial']=$_record->start_date;
		$info['cantidad']=$_record->total_days;
		$info['sucursal_trabajo']=$_record->personal_intelisis->branch_code;
		$info['empresa']=$_record->personal_intelisis->company_code;
		$info['referencia']=$_record->personal_intelisis->name.' '.$_record->personal_intelisis->last_name;
		$info['observaciones']="Periodo ".$_record->type_period;
		$info['concepto']="";

		$new_incidence = $this->IntelisisRepository->insertIncidence($info);
		return $new_incidence;

	}

	public function printDocumentVacation($_record_id){

		$data = $this->singleRecord($_record_id);

		// Se agrega la jornada del usuario
		$workday = $this->GeneralFunctionsRepository->getWorkDay($data->personal_intelisis->usuario_ad);
		$data->workday = $workday;

		//obtener pediodos
		$month_day = Carbon::parse($data->personal_intelisis->antiquity_date)->format('d-m');
		$periods = explode('-',$data->period);
		$period_text = $month_day.'-'.$periods[0].' al '.$month_day.'-'.$periods[1];

		$vacation_days_law= "";
		if(isset($data->data_vacation_days['vacation_days_law'])){
			$vacation_days_law = $data->data_vacation_days['vacation_days_law'];
		}else{
			$vacation_days_law = $data->data_vacation_days['available_periods'][0]['vacation_days_law']->days;
		}

        $pdf = PDF::loadView('PDF.DMI.RRHH.vacation',  [
            'data' => $data,
            'period_text' => $period_text,
			'vacation_days_law' => $vacation_days_law
        ], [], [
            'format' => 'A4',
        ]);

		$file_name = $data->id.'_'.time().'_vacation.pdf';
		$pdf->stream($file_name);



	}

}

