<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis;
use App\Models\DmiControlAuthorizationSignature;
use App\Models\DmiControlProcedureValidation;
use App\Models\Location;
use App\Repositories\ToolsRepository;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;
use App\Services\SendEmailService;
use App\Models\DmiControlProcess;
use App\Models\DmiControlPlazaSustitution;


class RecursosHumanosController extends Controller
{

	public function __construct()
	{
        $this->middleware('guest',['only'=>'ShowLogin']);

	}

	/* *********************** START - COORDINADOR POR UBICACION *********************** */
	public function listLocationCoordinator(Request $request){

		$order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;

		$list = DmiControlProcess::join("dmicontrol_authorization_signatures", "dmicontrol_authorization_signatures.dmi_control_process_id","dmicontrol_process.id")
									->join("personal_intelisis", "personal_intelisis.plaza_id","dmicontrol_authorization_signatures.plaza_id")
									->where("dmicontrol_process.name","Coordinadora_RRHH")
									->whereNull("dmicontrol_process.deleted_at")
									->whereNull("dmicontrol_authorization_signatures.deleted_at")
									->where("personal_intelisis.status","ALTA")
									->whereRaw("(personal_intelisis.name like '%$search%' or dmicontrol_process.location like '%$search%')")
									->select("dmicontrol_process.id as control_process_id","dmicontrol_process.name as name_process","dmicontrol_process.location",
											"dmicontrol_authorization_signatures.plaza_id",
											"personal_intelisis.name","personal_intelisis.last_name"
											)
									->orderBy('dmicontrol_process.created_at',$order_by)
									->Paginate($limit);

        $list->setPath('/rh/location-coordinator/list');

		return $list;

	}

	public function listLocation(){
		return Location::orderBy("name")->get();
	}

	public function rhStaffList(){
		return PersonalIntelisis::where("deparment","RECURSOS HUMANOS")->where("status","ALTA")->orderBy("name")->get();
	}

	public function saveRecord(Request $request){

		$request->validate([
			'plaza_id' => 'required|string|max:255',
			'location' => 'required|string|max:255',
		],[
			'plaza_id.required' => 'Seleccione un colaborador',
			'location.required' => 'Seleccione una ubicación',
		]);

		// Se verifica si ya existe ese coordinador y la ubicacion
		$exist_coordinator = DmiControlProcess::join("dmicontrol_authorization_signatures", "dmicontrol_authorization_signatures.dmi_control_process_id","dmicontrol_process.id")
												->where("dmicontrol_authorization_signatures.plaza_id",$request->plaza_id)
												->where("dmicontrol_process.location",$request->location)
												->where("dmicontrol_process.name","Coordinadora_RRHH")
												->first();

		// Se verifica que el colaborador cuente con usuario_ad
		$personal_intelisis = PersonalIntelisis::where("plaza_id",$request->plaza_id)->where("status","ALTA")->first();

		if($personal_intelisis->usuario_ad != null){
			if($exist_coordinator == null){
				$more_than_coordinator = DmiControlProcess::where("location",$request->location)
															->where("dmicontrol_process.name","Coordinadora_RRHH")
															->first();

				if($more_than_coordinator == null){

					$newRecord = new DmiControlProcess();
					$newRecord->name = "Coordinadora_RRHH";
					$newRecord->location = $request->location;
					$newRecord->save();

					$signature = new DmiControlAuthorizationSignature();
					$signature->plaza_id = $request->plaza_id;
					$signature->subsecId = 75;
					$signature->dmi_control_process_id = $newRecord->id;
					$signature->save();

					if($newRecord != null && $signature != null){
						//Se registran las validaciones que pertenecen a la coordinación

						$newProcedure = new DmiControlProcedureValidation();
						$newProcedure->seg_seccion_name= "vacaciones";
						$newProcedure->key= "Vacation_general_report_coordinador_rr_hh-usuario_ad";
						$newProcedure->value = $personal_intelisis->usuario_ad;
						$newProcedure->save();

						$newProcedure = new DmiControlProcedureValidation();
						$newProcedure->seg_seccion_name= "permisos";
						$newProcedure->key= "WorkPermit_general_report_coordinador_rr_hh-usuario_ad";
						$newProcedure->value = $personal_intelisis->usuario_ad;
						$newProcedure->save();

						$newProcedure = new DmiControlProcedureValidation();
						$newProcedure->seg_seccion_name= "cai";
						$newProcedure->key= "CAI_incident_proccess_coordinador_rr_hh-usuario_ad";
						$newProcedure->value = $personal_intelisis->usuario_ad;
						$newProcedure->save();



						return ["success" => 1, "data"=> $newRecord];
					}else{
						return ["success" => 0, "message" =>"Se a producido un error al registrarlo, intentelo de nuevo."];
					}

				}else{
					return ["success" => 2, "message" =>"Ya existe un Coordinador(a), en esta ubicación, necesita eliminar el actual."];
				}

			}else{
				return ["success" => 2, "message" =>"Actualmente ya existe este Coordinador en la Ubicación $request->location."];
			}
		}else{
			return ["success" => 2, "message" =>"El colaborador no cuenta con Usuario AD, contacta a tu Coordinadora de RRHH."];
		}



	}

	public function deleteLocationCoordinator($_control_process_id){

		$control_process = DmiControlProcess::where("id",$_control_process_id)->first();

		if($control_process != null){
			// Se elimina todo lo relacionado a la asignación de la coordinadora

			$authorization_signature = DmiControlAuthorizationSignature::where("dmi_control_process_id",$control_process->id)->delete();
			$pv_vacation = DmiControlProcedureValidation::where("key","Vacation_general_report_coordinador_rr_hh-usuario_ad")
																	->where('value',$control_process->usuario)
																	->delete();
			$pv_vacation = DmiControlProcedureValidation::where("key","WorkPermit_general_report_coordinador_rr_hh-usuario_ad")
																	->where('value',$control_process->usuario)
																	->delete();
			$pv_vacation = DmiControlProcedureValidation::where("key","CAI_incident_proccess_coordinador_rr_hh-usuario_ad")
																	->where('value',$control_process->usuario)
																	->delete();

			$control_process->delete();
			return ["success" => 1, "message" =>"Registro eliminado con éxito"];
		}else{
			return ["success" => 2, "message" =>"No existe el registro que intenta eliminar."];
		}

	}
	/* *********************** END - COORDINADOR POR UBICACION *********************** */


	/* *********************** START - REEMPLAZO TEMPORAL DE COLABORADOR *********************** */

	public function listReplaceCollaborator(Request $request){

		$order_by = isset($request->order_by) ? $request->order_by : 'desc';
        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';
        $search = isset($request->search) ? $request->search : null;

		$list = DmiControlPlazaSustitution::with('replaced','replace')
									->where(function($q) use ($search){
										return $q->whereRelation('replaced',function($q) use ($search){
											$q->orWhere('name','like',"%$search%")
												->orWhere('last_name','like',"%$search%");
										})
										->orWhereRelation('replace',function($q) use ($search){
											$q->orWhere('name','like',"%$search%")
												->orWhere('last_name','like',"%$search%");
										});
									})
									->orderBy('created_at',$order_by)
									->Paginate($limit);

        $list->setPath('/rh/replace-collaborator/list');

		return $list;
	}

	protected function getAllUsersActive(){
        if(Auth::check()){
            $res= PersonalIntelisis::where('status','ALTA')->whereNotNull("plaza_id")->where('plaza_id',"!=",'')->orderby('name')->get();

            return response()->json($res, 200);
        }else{
            return response()->json(null, 500);

        }

    }

	public function saveReplaceCollaborator(Request $request){

		$request->validate([
			'replaced_collaborator' => 'required|string|max:255',
			'replace_collaborator' => 'required|string|max:255',
		],[
			'replaced_collaborator.required' => 'Seleccione un colaborador',
			'replace_collaborator.required' => 'Seleccione una ubicación',
		]);

		$exist_replaced = DmiControlPlazaSustitution::where('plaza_id',$request->replaced_collaborator)->first();

		if($exist_replaced == null){

			$new_record = new DmiControlPlazaSustitution();
			$new_record->plaza_id = $request->replaced_collaborator;
			$new_record->substitute_plaza_id = $request->replace_collaborator;
			$new_record->save();

			return ["success" => 1, "data"=> $new_record];

		}else{
			return ["success" => 2, "message" =>"Este colaborador ya cuenta con un reemplazo actual."];
		}

	}

	public function deleteReplaceCollaborator(Request $request){
		$delete_record = DmiControlPlazaSustitution::where("id",$request->id)->first();

		if($delete_record != null){
			$delete_record->delete();
			return ["success" => 1, "message" =>"Registro eliminado con éxito"];
		}else{
			return ["success" => 2, "message" =>"No existe el registro que intenta eliminar."];
		}
	}

	/* *********************** END - REEMPLAZO TEMPORAL DE COLABORADOR *********************** */
}

