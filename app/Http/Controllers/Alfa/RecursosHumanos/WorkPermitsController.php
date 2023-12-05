<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis;
use App\Models\Dmirh\DmirhWorkPermit;
use App\Models\Dmirh\DmirhTypePermit;
use App\Models\Dmirh\DmirhPermitConcept;
use App\Models\DmiBucketSignature;
use App\Models\DmiControlProcedureValidation;
use App\Models\DmiControlSignaturesBehalfAudit;
use App\Models\DmiControlSignaturesBehalf;
use App\Models\DmiControlPlazaSustitution;
use App\Models\BucketFiles;
use App\Models\Files;
use App\Repositories\GeneralFunctionsRepository;
use App\Http\Controllers\GenericFunctionsController;
use App\Repositories\IntelisisRepository;
use App\Http\Requests\WorkPermitRequest;
use App\Repositories\ToolsRepository;
use DateTime;
use Illuminate\Support\Facades\Storage;
use App\Services\SendEmailService;
use Carbon\Carbon;
use PDF;
use App\Http\Controllers\NotificationCenterController;
use App\Models\DmiControlProcess;


class WorkPermitsController extends Controller
{
	private $toolRepository,$sendEmail;
	private $is_sign_on_behalf=false;

    public function __construct(ToolsRepository $toolRepository,SendEmailService $sendEmail, NotificationCenterController $notificationCenter){
        $this->middleware('guest',['only'=>'ShowLogin']);
		$this->sendEmail = $sendEmail;
		$this->GeneralFunctionsRepository = new GeneralFunctionsRepository();
		$this->GenericFunctionsController = new GenericFunctionsController();
		$this->IntelisisRepository = new IntelisisRepository();
		$this->SendEmailService = new SendEmailService();
		$this->toolRepository = $toolRepository;
		$this->seg_seccion = 9;
		$this->notificationCenter = $notificationCenter;

    }

    public function listWorkPermits(Request $request){

		$personal_intelisis_usuario_ad = isset($request->user) ? $request->user : Auth::user()->usuario;
        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $work_permits= [];


        if(isset($search) && strlen($search) > 0){
            $work_permits = DmirhWorkPermit::with(['type_permit','permit_concept','personal_intelisis.dmirh_personal_time'])
			->where('personal_intelisis_usuario_ad',$personal_intelisis_usuario_ad)
			->whereRaw("(reason like '%$search%'
                or date_request like '%$search%
                or status like '%$search%
                or id like '%$search%
            )")
            ->orderBy('date_request',$order_by)
			->Paginate($limit);
        }else{

            $work_permits = DmirhWorkPermit::with(['type_permit','permit_concept','personal_intelisis.dmirh_personal_time'])
			->where('personal_intelisis_usuario_ad',$personal_intelisis_usuario_ad)
            ->orderBy('date_request',$order_by)
            ->orderBy('id',$order_by)
            ->Paginate($limit);
        }

        $work_permits->setPath('/work-permitions/list');

        return $work_permits;


    }

    public function permitTypeList(){
        $list = DmirhTypePermit::all();
        if($list != null){
            return ["success" => 1, "data"=> $list];
        }else{
            return ["success" => 0, "message" =>"Error al obtener los tipos de permisos"];
        }


    }

    public function permitConceptList($_type_concept_id){
        $list = DmirhPermitConcept::where('dmirh_type_permits_id',$_type_concept_id)->get();
        if($list != null){
            return ["success" => 1, "data"=> $list];
        }else{
            return ["success" => 0, "message" =>"Error al obtener los tipos de permisos"];
        }
    }


    public function workPermitAdd(WorkPermitRequest $request){

		// Se valida el tipo de permiso
		$validate_type_permit = $this->validateWorkPermit($request);

		if($validate_type_permit['success'] == 1){
			$recordNew = new DmirhWorkPermit();
			$recordNew->personal_intelisis_usuario_ad = $request->personal_intelisis_usuario_ad;
			$recordNew->personal_id = Auth::user()->personal_intelisis->personal_id;
			$recordNew->dmirh_type_permits_id = $request->type_permit_id;
			$recordNew->dmirh_permit_concepts_id = $request->permit_concept_id > 0 ? $request->permit_concept_id : null;
			$recordNew->start_date = $request->start_date;
			$recordNew->end_date = $request->end_date;
			$recordNew->return_date = $request->end_date;
			$recordNew->total_days = $request->total_days;
			$recordNew->date_request = Carbon::now();
			$recordNew->reason = $request->permit_concept_id > 0 ? $request->permit_concept_id : $request->reason;
			$recordNew->comments = $request->comments;
			$recordNew->status = "solicitado";


			$has_signatures = $this->hasSignatures($recordNew);
			if($has_signatures['success'] == 1){

				$recordNew->save();

				foreach($has_signatures['data'] as $key => $sign){
					$sign_employee = new DmiBucketSignature();
					$sign_employee->origin_record_id = $recordNew->id;
					$sign_employee->seg_seccion_id = $this->seg_seccion;
					$sign_employee->personal_intelisis_usuario_ad = $sign['usuario_ad'];
					$sign_employee->order = $sign['order'];
					$sign_employee->status = $sign['status'];
					$sign_employee->signed_date = $sign['signed_date'];
					$sign_employee->is_automatic = $sign['is_automatic'];
					$sign_employee->exists_signatures_behalves_audit = (isset($sign['by_plaza_sustitution']) && $sign['by_plaza_sustitution'] != null ? 1 : null);
					$sign_employee->save();

					if(isset($sign['by_plaza_sustitution']) && $sign['by_plaza_sustitution'] != null){
						$audit_signature = new DmiControlSignaturesBehalfAudit();
						$audit_signature->origin_record_id = $recordNew->id;
						$audit_signature->seg_seccion_id = $this->seg_seccion;
						$audit_signature->sign_behalf_usuario_ad = $sign['usuario_ad'];
						$audit_signature->signature_order = $sign['order'];
						$audit_signature->dmi_bucket_signatures_id = $sign_employee->id;
						$audit_signature->origin_table = 'control_plaza_substitution';
						$audit_signature->origin_table_id = $sign['by_plaza_sustitution']->id;
						$audit_signature->save();
					}
				}

				// Se termina el registro y se regresa el registro completo
				if($recordNew != null){

					// Se revisa si tiene archivos para cargar
					if(isset($request->total_document_uploaded) && $request->total_document_uploaded > 0){
						// START - Se guardan los archivos cargados
						$folder_path_storage = 'storage/Publico/pdf/dmi_rrhh/work_permit/attach_support/';
						$folder_path = 'pdf/dmi_rrhh/work_permit/attach_support/';
						if(!file_exists($folder_path_storage)) {
							mkdir($folder_path_storage, 0777,true);
						}

						// Se almacenan los documentos
						for ($i=0; $i < $request->total_document_uploaded; $i++) {
							$file = $request->file('attach_doc_'.$i);
							$file_name = time().'_'.$i."_".$file->getClientOriginalName();
							$path = $folder_path.$file_name;
							\Storage::disk('Publico')->put($path,\File::get($file));

							$new_file = new Files();
							$new_file->name = $file->getClientOriginalName();
							$new_file->url = $folder_path_storage.$file_name;
							$new_file->extension = $file->getClientOriginalExtension();
							$new_file->save();

							$bucket_file = new BucketFiles();
							$bucket_file->seg_seccion_id = $this->seg_seccion;
							$bucket_file->origin_record_id = $recordNew->id;
							$bucket_file->file_id = $new_file->id;
							$bucket_file->save();
						}


					}

					$recordComplete = $this->singleWorkPermits($recordNew->id);
					return ["success" => 1, "data"=> $recordComplete];
				}else{
					return ["success" => 0, "message" =>"Se a producido un error al intenta, intentelo de nuevo."];
				}

			}else{
				return ["success" => 2, 'message' => $has_signatures['message']];
			}
		}else{
			return ["success" => 2, 'message' => $validate_type_permit['message']];
		}




    }

	public function validateWorkPermit($_request_workpermit){

		$type_permit =  DmirhTypePermit::where('id',$_request_workpermit->type_permit_id)->first();
		$permit_concept =  DmirhPermitConcept::where('id',$_request_workpermit->permit_concept_id)->first();
		$validations = 0;
		$message = "";

		//validacion de nupcias
		if(isset($permit_concept->description) && $permit_concept->description == 'Primeras nupcias'){
			$work_permit_nuptials = DmirhWorkPermit::where('dmirh_type_permits_id',$type_permit->id)
													->where('dmirh_permit_concepts_id',$permit_concept->id)
													->where('personal_intelisis_usuario_ad',$_request_workpermit->personal_intelisis_usuario_ad)
													->whereIn('status',['solicitado','autorizado'])
													->get();
			if(sizeof($work_permit_nuptials) > 0){
				$validations++;
				$message = "No puede solicitar más de un permiso por Primeras Nupcias";
			}
		}

		if(isset($permit_concept->description) && $permit_concept->description == 'Enfermedad'){
			$work_permit_nuptials = DmirhWorkPermit::where('dmirh_type_permits_id',$type_permit->id)
													->where('dmirh_permit_concepts_id',$permit_concept->id)
													->where('personal_intelisis_usuario_ad',$_request_workpermit->personal_intelisis_usuario_ad)
													->whereIn('status',['solicitado','autorizado'])
													->get();
			if(sizeof($work_permit_nuptials) >= 3){
				$validations++;
				$message = "No puede solicitar más de 3 permiso por Enfermedad";
			}
		}

		if($validations == 0){
			return ['success' => 1, 'message' => ""];
		}else{
			return ['success' => 0, 'message' => $message];
		}


	}

	public function hasSignatures($_record_saved){

		$creat_signatures = [];
		$signing_users=[];
		$order = 1;

		$profile_employee = PersonalIntelisis::where('usuario_ad',$_record_saved->personal_intelisis_usuario_ad)
							->where('status','ALTA')
							->first();

		$signing_users[]=['job' =>'profile_employee',
						'usuario_ad' => $profile_employee->usuario_ad,
						'order' => $order,
						'status' => 'pendiente',
						'signed_date' => null,
						'is_automatic' => 0,
						'automatic_signature' => false];



		if($profile_employee->top_plaza_id != null){
			$temp_plaza_immediate_boss = $this->GeneralFunctionsRepository->getContolPlazaSubstitution($profile_employee->top_plaza_id);
			$plaza_immediate_boss = null;
			$by_plaza_sustitution = null;

			if($temp_plaza_immediate_boss != null){
				$plaza_immediate_boss = $temp_plaza_immediate_boss->substitute_plaza_id;
				$by_plaza_sustitution = $temp_plaza_immediate_boss;
			}else{
				$plaza_immediate_boss = $profile_employee->top_plaza_id;
			}
			$immediate_boss = PersonalIntelisis::where('plaza_id',$plaza_immediate_boss)
												->where('status','ALTA')
												->first();

			// Se revisa si cuenta con jefe directo asignado, ya que el aparece en todo
			if($immediate_boss != null){
				$order++;
				$signing_users[]=['job' =>'immediate_boss',
								  'usuario_ad' => $immediate_boss->usuario_ad,
								  'order' => $order,
								  'status' => null,
								  'signed_date' => null,
								  'is_automatic' => 0,
								  'automatic_signature' => false,
								  'by_plaza_sustitution' => $by_plaza_sustitution];


				//Se busca si existe una Firma que Libera Tanto director o coordinador
				$type_permit =  DmirhTypePermit::where('id',$_record_saved->dmirh_type_permits_id)->select('description')->pluck('description')->first();
				$permit_concept =  DmirhPermitConcept::where('id',$_record_saved->dmirh_permit_concepts_id)->select('description')->pluck('description')->first();
				$value= $type_permit.'_'.$permit_concept;
				$exist_release_signature_director = DmiControlProcedureValidation::where('key','workpermit_exist_release_signature_director-type_concept')->where('value',$value)->first();
				$exist_release_signature_dpto_rrhh = DmiControlProcedureValidation::where('key','workpermit_exist_release_signature_coordinador_rrhh-type_concept')->where('value',$value)->first();

				// Se obtine al director
				if($exist_release_signature_director != null){
					$director = $this->GeneralFunctionsRepository->getDirector($profile_employee->usuario_ad);
					if($director != null){
						$order++;
						$signing_users[]=['job' =>'director',
										  'usuario_ad' => $director->usuario_ad,
										  'order' => $order,
										  'status' => null,
										  'signed_date' => null,
										  'is_automatic' => 0,
										  'automatic_signature' => false,
										  'by_plaza_sustitution' => $director['by_plaza_sustitution']];
					}else{
						return ["success" => 2, 'message' => "No cuenta con director asignado en su Unidad de Negocio, contacta a tu Coordinadora de RRHH."];
					}

				}

				// Se obtine al coordinador de rrhh
				$temp_plaza_dpto_rrhh=$this->GeneralFunctionsRepository->getControlAuthorizationSignatures('Coordinadora_RRHH',$profile_employee->location);

				if($temp_plaza_dpto_rrhh != null){
					$dpto_rrhh = PersonalIntelisis::where('plaza_id',$temp_plaza_dpto_rrhh->plaza_id)
										->where('status','ALTA')
										->first();
					// En caso de no tener plaza el personal, se utiliza el usuario_ad que se guardo en el mismo campo
					if($dpto_rrhh == null){
						$dpto_rrhh = PersonalIntelisis::where('usuario_ad',$temp_plaza_dpto_rrhh->plaza_id)->where('status','ALTA')->first();
					}
					
					if($dpto_rrhh != null){
						$order++;

						if($exist_release_signature_dpto_rrhh != null){
							$signing_users[]=['job' =>'dpto_rrhh',
											  'usuario_ad' => $dpto_rrhh->usuario_ad,
											  'order' => $order,
											  'status' => null,
											  'signed_date' => null,
											  'is_automatic' => $exist_release_signature_dpto_rrhh != null ? 0 : 1,
											  'automatic_signature' => $exist_release_signature_dpto_rrhh != null ? false : true,
											  'by_plaza_sustitution' => $temp_plaza_dpto_rrhh->by_plaza_sustitution];
						}else{
							$signing_users[]=['job' =>'dpto_rrhh',
											  'usuario_ad' => $dpto_rrhh->usuario_ad,
											  'order' => $order,
											  'status' => 'firmado',
											  'signed_date' => Carbon::now(),
											  'is_automatic' => $exist_release_signature_dpto_rrhh != null ? 0 : 1,
											  'automatic_signature' => $exist_release_signature_dpto_rrhh != null ? false : true,
											  'by_plaza_sustitution' => $temp_plaza_dpto_rrhh->by_plaza_sustitution];
						}

						return ['success'=> 1, 'data' => $signing_users];


					}else{
						return ["success" => 2, 'message' => "No cuenta con coordinador de RRHH asignado en su Unidad de Negocio, contacta a Gerente de RRHH."];
					}
				}else{
					return ["success" => 2, 'message' => "No cuenta con coordinador de RRHH asignado en su Unidad de Negocio, contacta a Gerente de RRHH."];
				}

			}else{
				return ["success" => 2, 'message' => "No cuenta con jefe inmediato asignado, contacta a tu Coordinadora de RRHH."];
			}

		}else{
			return ["success" => 2, 'message' => "No cuenta con jefe inmediato asignado, contacta a tu Coordinadora de RRHH."];
		}

	}


    public function singleWorkPermits($_id){

        $record = DmirhWorkPermit::with(['type_permit','permit_concept','personal_intelisis.dmirh_personal_time'])->find($_id);
        return $record;

    }

    public function signWorkPermit(Request $request){

        $personal_intelisis_usuario_ad = $request->personal_intelisis_usuario_ad;



        $sign = DmiBucketSignature::where('origin_record_id',$request->work_permit_id)
                        ->where('seg_seccion_id',$this->seg_seccion)
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
						$signature_behalf = DmiControlSignaturesBehalf::where('seg_seccion_id',$this->seg_seccion)->where('behalf_usuario_ad',auth()->user()->usuario)->first();
						
						$audit = new DmiControlSignaturesBehalfAudit();
						$audit->origin_record_id = $request->work_permit_id;
						$audit->seg_seccion_id = $this->seg_seccion;
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

                        $signatures = DmiBucketSignature::where('origin_record_id',$request->work_permit_id)
                                        ->where('seg_seccion_id',$this->seg_seccion)
                                        ->get();

                        $end_sign = sizeof($signatures)-1;
                        $active_sign = false;
                        foreach ($signatures as $key => $item) {
                            if($item->status == "firmado"){
                                if($end_sign == $key){
                                    // Se verifica si se completaron todas las firmas y se cambia el estatus al permiso en caso de estar todas las firmas
                                    $verify_signatures = DmiBucketSignature::where('origin_record_id',$request->work_permit_id)
                                        ->where('seg_seccion_id',$this->seg_seccion)
                                        ->get();

                                    $cont_sign = 0;
                                    foreach($verify_signatures as $key => $v_item){
                                        if($v_item->status == "firmado" && $v_item->signed_date != null){
                                            $cont_sign++;
                                        }
                                    }

                                    if($cont_sign == sizeof($verify_signatures)){
                                        $permit = DmirhWorkPermit::with(['personal_intelisis','type_permit','permit_concept'])->where('id',$request->work_permit_id)->first();
                                        $permit->status = "autorizado";
                                        $permit->save();

										// Se registran los permisos en intelisis y se guarda el número de movimiento de intelisis al que pertenece
										if($permit->dmirh_type_permits_id == 3 && $permit->dmirh_permit_concepts_id == 11){
                                        	// Se verifica que si es permiso por día de cumple, ese no se afecta al intelisis
										}else{
											$insert_incidence = $this->sendWorkPermitIntelisis($permit);
											$permit->mov_intelisis = $insert_incidence;
											$permit->save();
										}

                                        $this->generarPDFWorkPermit($request->work_permit_id);
                                        $this->sendConfirmationEmail($request->work_permit_id);

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
															"Permisos",
															"Tiene una solicitud de permisos por firmar con folio ".$request->work_permit_id.", consulta para mas detalles...",
															"notification",
															"rh/permisos-trabajo/autorizar-permisos",
															"notification",
															"media"
														);

								break;
                            }

                        }

                    }else if($status == "rechazado"){
                        // Se eliminan cancelan las demas firmas
                        $this->rejectedSignature($request->work_permit_id);
                    }
                    $work_permit = $this->singleWorkPermits($request->work_permit_id);
                    return ['success' => 1, 'data'=>$work_permit, "action" => $status];
                }
            }else{
                return ['success' => 0, 'message'=> 'El estatus no enviado no es correcto.'];
            }


        }else{
            return ['success' => 0, 'message'=> 'No se encontró la firma solicitada.'];
        }

    }

    public function authorizePermitList(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $profile = PersonalIntelisis::where("usuario_ad",Auth::user()->usuario)
									->where('status','ALTA')
									->first();

		// Se verifica si puede firmar en lugar de alguien más
		$check_signature_behalf = $this->GenericFunctionsController->checkUserSignOnBehalf($this->seg_seccion,$profile->usuario_ad);

        if(isset($search) && strlen($search) > 0){

            $list = DmirhWorkPermit::with(['personal_intelisis.dmirh_personal_time','type_permit','permit_concept'])
                                    ->join('dmi_bucket_signatures as bs','bs.origin_record_id','=','dmirh_work_permits.id')
                                    ->where('bs.seg_seccion_id',$this->seg_seccion)
                                    ->where('dmirh_work_permits.status','solicitado')
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
											->orWhereRaw("(dmirh_work_permits.date_request like '%$search%'
												or dmirh_work_permits.status like '%$search%'
												or dmirh_work_permits.id like '%$search%'
											)");
										}
									)
                                    ->orderBy('dmirh_work_permits.created_at',$order_by)
                                    ->selectRaw('dmirh_work_permits.*')
                                    ->Paginate($limit);
        }else{

            $list = DmirhWorkPermit::with(['personal_intelisis.dmirh_personal_time','type_permit','permit_concept'])
                                    ->join('dmi_bucket_signatures as bs','bs.origin_record_id','=','dmirh_work_permits.id')
                                    ->where('bs.seg_seccion_id',$this->seg_seccion)
                                    ->where('dmirh_work_permits.status','solicitado')
                                    ->where('bs.status','pendiente');

									if($check_signature_behalf['is_valid'] == 1){
										$list = $list->whereIn('bs.personal_intelisis_usuario_ad',array_merge($check_signature_behalf['data'],[$profile->usuario_ad]));
									}else{
										$list = $list->where('bs.personal_intelisis_usuario_ad',$profile->usuario_ad);
									}

                                    $list = $list->whereNull('bs.signed_date')
                                    ->where('bs.order', '>=' , 2)
                                    ->orderBy('dmirh_work_permits.created_at',$order_by)
                                    ->selectRaw('dmirh_work_permits.*')
                                    ->Paginate($limit);
        }

        $list->setPath('/rh/work-permitions/authorize-permit-list');

        return $list;




    }

    public function staffPermitList(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $staff_personal_intelisis_usuario_ad = [];
        $profile = PersonalIntelisis::with('commanding_staff')
									->where("usuario_ad",Auth::user()->usuario)
									->where('status','ALTA')
									->first();

        $this->staff_personal_intelisis_usuario_ads = [];

        if(sizeof($profile->commanding_staff) > 0){
			$this->get_staff($profile);
		}

        if(isset($search) && strlen($search) > 0){
            $list = DmirhWorkPermit::with(['personal_intelisis.dmirh_personal_time','type_permit','permit_concept'])
									->where(
										function ($q) use ($search, $order_by){
											return $q->whereRelation('personal_intelisis',function($q) use ($search){
												return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
											})
											->orWhereRaw("(dmirh_work_permits.reason like '%$search%'
														or dmirh_work_permits.date_request like '%$search%'
														or dmirh_work_permits.status like '%$search%'
														or dmirh_work_permits.id like '%$search%'
													)");
									})
                                    ->orderBy('dmirh_work_permits.created_at',$order_by)
                                    ->whereIn('personal_intelisis_usuario_ad',$this->staff_personal_intelisis_usuario_ads)
                                    ->selectRaw('dmirh_work_permits.*')
                                    ->Paginate($limit);
        }else{

            $list = DmirhWorkPermit::with(['personal_intelisis.dmirh_personal_time','type_permit','permit_concept'])
                                    ->whereIn('personal_intelisis_usuario_ad',$this->staff_personal_intelisis_usuario_ads)
                                    ->orderBy('dmirh_work_permits.created_at',$order_by)
                                    ->selectRaw('dmirh_work_permits.*')
                                    ->Paginate($limit);
        }

        $list->setPath('/rh/work-permitions/staff-permit-list');

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

    public function rejectedSignature($_work_permit_id){
        $cancel_signatures = DmiBucketSignature::where('origin_record_id',$_work_permit_id)
                        ->where('seg_seccion_id',$this->seg_seccion)
                        ->get();

        foreach($cancel_signatures as $key => $sign){
            if($sign->signed_date == null){
                $sign->signed_date = Carbon::now();
                $sign->status = "cancelado";
                $sign->save();
            }
        }

        // Y se rechaza el permiso tambien
        $permit = DmirhWorkPermit::find($_work_permit_id);
        $permit->status = "rechazado";
        $permit->save();

        $this->sendConfirmationEmail($permit->id);
    }

    public function generalReport(Request $request){

        $order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;
        $start_date = isset($request->start_date) ? $request->start_date : null;
        $end_date = isset($request->end_date) ? $request->end_date : null;
        $type_permit = isset($request->type_permit) ? $request->type_permit : null;
        $status = isset($request->status) ? $request->status : null;

		//Se verifica que el usuario sea coordinador
		$exist_user_coordinador = DmiControlProcedureValidation::where('key','WorkPermit_general_report_coordinador_rr_hh-usuario_ad')->where('value',Auth::user()->usuario)->first();

		//Se obtiene la locación del usuario de rrhh para solo mostrar registros de su locacion
		/* $location_user_rrhh = PersonalIntelisis::where('usuario_ad',Auth::user()->usuario)->where('status','ALTA')->pluck('location')->first();
		$users_location= PersonalIntelisis::where('location',$location_user_rrhh)->where('status','ALTA')->whereNotNull('usuario_ad')->pluck('usuario_ad'); */
		
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

        if((isset($search) && strlen($search) > 0) || $start_date != null || $end_date != null || $status != null || $type_permit != null){

            $start_date = isset($request->start_date) ? Carbon::parse($request->start_date)->format('Y-m-d') : '2000-01-01';
            $end_date = isset($request->end_date) ? Carbon::parse($request->end_date)->format('Y-m-d') : (Carbon::now())->format('Y-m-d');
            $status = isset($request->status) ? $request->status : '';
            $type_permit = isset($request->type_permit) ?  $request->type_permit : '';

            $list = DmirhWorkPermit::with(['personal_intelisis.dmirh_personal_time','type_permit','permit_concept'])
											->whereRelation('personal_intelisis',function($q) use ($search){
												return $q->orWhere('name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
											})
											->where('dmirh_type_permits_id', 'like', "%$type_permit%");

											if($exist_user_coordinador != null){
												$list = $list->whereIn('personal_intelisis_usuario_ad',$users_location);
											}

											$list = $list->where('status','like',"%$status%")
											->whereDate("date_request",">=",$start_date)
											->whereDate("date_request","<=",$end_date)
											->orderBy('date_request',$order_by)
											->Paginate($limit);
        }else{
            $list = DmirhWorkPermit::with(['personal_intelisis.dmirh_personal_time','type_permit','permit_concept']);

									if($exist_user_coordinador != null){
										$list = $list->whereIn('personal_intelisis_usuario_ad',$users_location);
									}
                                    $list = $list->orderBy('date_request',$order_by)
                                    ->Paginate($limit);
        }

        $list->setPath('/rh/work-permitions/general-report');

        return $list;
    }

    public function generarPDFWorkPermit($_id){

        //$_id = 4;
		$year = Carbon::now()->format("Y");
        $data = $this->singleWorkPermits($_id);

		// Se agrega la jornada del usuario
		$workday = $this->GeneralFunctionsRepository->getWorkDay($data->personal_intelisis->usuario_ad);
		$data->workday = $workday;

        $pdf = PDF::loadView('PDF.DMI.RRHH.work_permit',  [
            'data' => $data,
        ], [], [
            'format' => 'A4',
        ]);

		//return $pdf->stream('PDF.DMI.RRHH.work_permit');
		//return view("PDF.DMI.RRHH.work_permit")->with(compact("data"));

		if($this->is_sign_on_behalf == true){
			$path_folder="";
			$path_folder="storage/Council_Documents/work_permit";
			if(!file_exists($path_folder)) {
				mkdir($path_folder, 0700,true);
			}
			$file_name = $data->id.'_'.time().'_work_permit.pdf';
			$path = "{$path_folder}/{$file_name}";
			$pdf->save(public_path($path));

		}
		$path_folder="storage/Publico/pdf/dmi_rrhh/work_permit";
		$file_name = $this->toolRepository->getFilename($_id, $data->personal_intelisis, $data->start_date);

		$path = "{$year}/Permisos/{$data->personal_intelisis->company_code}/{$data->personal_intelisis->branch_code}/{$file_name}";

		Storage::disk('IncidenciasIntranet')->put($path, $pdf->output());

		$tmp_path = "storage/IncidenciasIntranet/{$path}";


        $work_permit = DmirhWorkPermit::where('id',$_id)->first();
		$work_permit->document = $tmp_path;
		$work_permit->save();

    }

    public function cancelWorkPermit($_id){

        $work_permit = DmirhWorkPermit::find($_id);
        if($work_permit != null){

            //Se verifica, si esta autorizado, se manda cancelar tambien hacia intelisis
            if($work_permit->status == "autorizado"){
                // Aqui se agrega la petición para cancelar en intelisis
                $response_intelisis = $this->cancelarWorkPermitIntelisis($work_permit->mov_intelisis);

				// Estatus que puede retornar Intelisis
				//  -PROCESADA EN NOMINA
				//  -ERROR AL CANCELAR
				//  -CANCELADA
				//  -ELIMINADA

				if($response_intelisis == "CANCELADA"){
					$work_permit->status = "cancelado";
					$work_permit->save();

					$this->generarPDFWorkPermit($work_permit->id);
					$this->sendConfirmationEmail($work_permit->id);
					$this->GeneralFunctionsRepository->deleteFile(['url'=>$work_permit->document]);

					return ['success' => 1, 'message' => "Registro cancelado con éxito"];

				}else{
					return ['success' => 2, 'message' => "No se pudo tramitar la cancelación, contacta con el Coordinador tu de RRHH"];
				}


            }else{
				$work_permit->status = "cancelado";
				$work_permit->save();

				$this->sendConfirmationEmail($work_permit->id);

				return ['success' => 1, 'message' => "Registro cancelado con éxito"];
			}

        }else{
            return ['success' => 0, 'message' => "El registro no ha sido encontrado."];
        }


    }

    public function cancelarWorkPermitIntelisis($_mov_intelisis){
		$cancel_intelisis = $this->IntelisisRepository->cancelIncidence($_mov_intelisis);
		return $cancel_intelisis;

    }

    public function sendConfirmationEmail($_work_permit_id){

		$permit = DmirhWorkPermit::with('personal_intelisis')->where('id',$_work_permit_id)->first();

        $subject="Permiso de Trabajo";
        $name=strtoupper($permit->personal_intelisis->name.' '.$permit->personal_intelisis->last_name);
        $email=$permit->personal_intelisis->email;

        $data = [
                    'data' =>[
						'id' => $permit->id,
						'date' => $permit->updated_at,
						'subject' => $subject,
						'name' => $name,
						'status' => strtoupper($permit->status),
					],
                    'module' => "work_permit",
                    'to_email' => $email,
                ];

		$this->SendEmailService->notificationEmailRRHH($data);

		//Se agrega la notificación para el Front
		$type_notification = $permit->status == "autorizado" ? "approved_request" : "cancel_request";

		$this->notificationCenter->addNotificationCenter(
			$permit->personal_intelisis_usuario_ad,
			"Permiso",
			"La solicitud de Permiso con folio ".$permit->id." ha sido $permit->status, consulta para mas detalles...",
			"notification",
			"rh/permisos-trabajo/mis-permisos",
			$type_notification,
			"media");

		if($permit->status == "autorizado"){
			// se envia notificación
			// Se obtine al coordinador de rrhh
			$temp_plaza_dpto_rrhh=$this->GeneralFunctionsRepository->getControlAuthorizationSignatures('Coordinadora_RRHH',$permit->personal_intelisis->location);
			$coordinador = PersonalIntelisis::where('plaza_id',$temp_plaza_dpto_rrhh->plaza_id)
										->where('status','ALTA')
										->first();

			$data = [
				'data' =>[
					'id' => $permit->id,
					'date' => $permit->updated_at,
					'subject' => $subject,
					'name' => $name,
					'name_coordinador' => $coordinador->name." ".$coordinador->last_name,
					'status' => strtoupper($permit->status),
				],
				'module' => "work_permit_notification",
				'to_email' => $coordinador->email,
			];

			//dd($data);
			$this->SendEmailService->notificationEmailRRHH($data);
		}

    }

	public function sendWorkPermitIntelisis($_record){

		$type_permit= $_record->type_permit->with_pay == 1 ? 'Con goce de sueldo' : 'Sin goce de sueldo';
		$reason = $_record->permit_concept == null ? $_record->reason : $_record->permit_concept->description;

		$info['tipo_incidencia']="p";
		$info['personal']=$_record->personal_intelisis->personal_id;
		$info['fecha_inicial']=$_record->start_date;
		$info['cantidad']=$_record->total_days;
		$info['sucursal_trabajo']=$_record->personal_intelisis->branch_code;
		$info['empresa']=$_record->personal_intelisis->company_code;
		$info['referencia']=$_record->personal_intelisis->name.' '.$_record->personal_intelisis->last_name;
		$info['observaciones']=$reason.' - '.$_record->comments;
		$info['concepto']=$type_permit;

		$new_incidence = $this->IntelisisRepository->insertIncidence($info);
		return $new_incidence;

	}

	public function printDocumentWorkPermit($_record_id){

		$data = $this->singleWorkPermits($_record_id);

		// Se agrega la jornada del usuario
		$workday = $this->GeneralFunctionsRepository->getWorkDay($data->personal_intelisis->usuario_ad);
		$data->workday = $workday;

		//return ($data);
        $pdf = PDF::loadView('PDF.DMI.RRHH.work_permit',  [
            'data' => $data,
        ], [], [
            'format' => 'A4',
        ]);

        $file_name = $data->id.'_'.time().'_work_permit.pdf';
		$pdf->stream($file_name);


	}



}
