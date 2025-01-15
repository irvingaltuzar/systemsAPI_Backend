<?php

namespace App\Repositories;

use App\Models\PersonalIntelisis;
use App\Models\DmiControlPlazaSustitution;
use App\Models\DmiControlAuthorizationSignature;
use App\Models\VwDmiPersonalPlaza;
use Illuminate\Support\Facades\DB;

class GeneralFunctionsRepository
{

	public $previous_plaza_top;
	public $staff_personal_intelisis=[];
	public $get_field = "";

	public function getContolPlazaSubstitution($_plaza_id){
		$substitute_plaza = DmiControlPlazaSustitution::where('plaza_id',$_plaza_id)->first();
		if($substitute_plaza != null){
			return $substitute_plaza;
		}else{
			return null;
		}


	}

	public function getControlAuthorizationSignatures($_control_process_name,$_location=null){

		$control_authorization_signatures = DmiControlAuthorizationSignature::join('dmicontrol_process','dmicontrol_authorization_signatures.dmi_control_process_id','=','dmicontrol_process.id')
																->where('dmicontrol_process.name',$_control_process_name)
																->whereNull('dmicontrol_process.deleted_at');
																if($_location != null){
																	$control_authorization_signatures = $control_authorization_signatures->where('location',$_location);
																}
																$control_authorization_signatures = $control_authorization_signatures->select('dmicontrol_authorization_signatures.*')->first();

		// Se verifica que la plaza no tenga a un sustituto para su firma
		if($control_authorization_signatures != null){
			$top_substitution = $this->getContolPlazaSubstitution($control_authorization_signatures->plaza_id);
			if($top_substitution != null){
				$new_plaza = PersonalIntelisis::where('plaza_id',$top_substitution->substitute_plaza_id)->first();
				$new_plaza->by_plaza_sustitution = $top_substitution;
				return $new_plaza;
			}else{
				$control_authorization_signatures->by_plaza_sustitution = null;
				return $control_authorization_signatures;
			}
		}else{
			return null;
		}


	}

	public function getDirector($_personal_intelisis_usuario_ad){
		try{	
			$get_director = PersonalIntelisis::with("get_higher")->where('usuario_ad',$_personal_intelisis_usuario_ad)->where('status','ALTA')->first();
			if($get_director->get_higher != null){
				$director = $this->haveTopPlaza($get_director,"DIRECTOR");
				$director = $director === false ? $this->previous_plaza_top : $director;

				// Se verifica que la plaza no tenga a un sustituto para su firma
				$top_substitution = $this->getContolPlazaSubstitution($director->plaza_id);
				if($top_substitution != null){
					$sust_director = PersonalIntelisis::where('plaza_id',$top_substitution->substitute_plaza_id)->first();
					$sust_director->by_plaza_sustitution = $top_substitution;
					return $sust_director;
				}else{
					$sust_director['by_plaza_sustitution'] = null;
					return $director;
				}

			}else{
				return null;

			}

		}catch(\Exception $exc){
			return null;
		}

        
    }

    public function haveTopPlaza($_personal_intelisis,$_puesto){
        $top = $_personal_intelisis->get_higher;

		if($top != null){
			if(gettype(strpos(strtoupper($top->position_company_full),$_puesto)) === "integer"){
				return $top;
			}else if($top->position_company_full == null){
				if($top->top_plaza_id == null && strpos(strtoupper($_personal_intelisis->position_company_full),$_puesto) !== false ){
					return $top;
				}else{
					return null;
				}
			}else{
				$this->previous_plaza_top = $top;
				return $this->haveTopPlaza($top,$_puesto);
			}
		}else{
			return null;
		}



    }

	public function deleteFile($_params){

        if($_params['url'] != null){
            \File::delete($_params['url']);
        }

        return 1;

    }

	public function getWorkDay($_usuario_ad){
		$personal_intelisis = PersonalIntelisis::with('dmirh_personal_time')->where('usuario_ad',$_usuario_ad)->first();

		if(isset($personal_intelisis->dmirh_personal_time) != null){
			$personal_time = $personal_intelisis->dmirh_personal_time;
			if($personal_time != null){
				if($personal_time->dmirh_personal_time_details){
					$week_day = ['','L','M','M','J','V','S','D'];
					$working_day_complete = "";

					foreach($personal_time->dmirh_personal_time_details as $key => $time){
						$working_day_complete .= $week_day[$time->week_day];

						if(sizeof($personal_time->dmirh_personal_time_details)-1 != $key ){
							$working_day_complete .=" - ";
						}
					}
					return $working_day_complete;
				}else{
					return "No se encontro jornada disponible";
				}

			}else{
				return "No se encontro jornada disponible";
			}
		}else{
			return "No se encontro jornada disponible";
		}

	}

	public function getCoordinadoraRRHH($_location){
		// Se obtine al coordinador de rrhh
		$temp_plaza_dpto_rrhh=$this->getControlAuthorizationSignatures('Coordinadora_RRHH',$_location);

		if($temp_plaza_dpto_rrhh != null){
			$dpto_rrhh = PersonalIntelisis::where('plaza_id',$temp_plaza_dpto_rrhh->plaza_id)
								->where('status','ALTA')
								->first();

			// En caso de no tener plaza el personal, se utiliza el usuario_ad que se guardo en el mismo campo
			if($dpto_rrhh == null){
				$dpto_rrhh = PersonalIntelisis::where('usuario_ad',$temp_plaza_dpto_rrhh->plaza_id)->where('status','ALTA')->first();
			}
			
			if($dpto_rrhh != null){

				return ['success'=> 1, 'data' => $dpto_rrhh];

			}else{
				return ["success" => 2, 'message' => "No cuenta con coordinador de RRHH asignado en su Unidad de Negocio, contacta a Gerente de RRHH."];
			}
		}else{
			return ["success" => 2, 'message' => "No cuenta con coordinador de RRHH asignado en su Unidad de Negocio, contacta a Gerente de RRHH."];
		}
	}

	public function getGerente($_personal_intelisis_usuario_ad){

		try{

			$get_gerente = VwDmiPersonalPlaza::with("get_higher")->where('usuario_ad',$_personal_intelisis_usuario_ad)->where('status','ALTA')->first();
			if($get_gerente != null){
				if($get_gerente->get_higher != null){
					$gerente = $this->haveTopPlaza($get_gerente,"GERENTE");
					$gerente = $gerente === false ? $this->previous_plaza_top : $gerente;
					return $gerente;
		
				}else{
					return null;
				}
			}else{
				return null;
			}
		}catch(\Exepction $exc){
			return null;
		}

		
		
    }

	public function getCommandingStaff($_plaza_id,$_get_field = 'rfc'){

		$profile = PersonalIntelisis::with('commanding_staff')->where("plaza_id",$_plaza_id)->where('status','ALTA')->first();
		$this->get_field = $_get_field;

		if(sizeof($profile->commanding_staff) > 0){
			$this->get_staff($profile);
		}
		
		return $this->staff_personal_intelisis;

	}

	public function get_staff($_staff){

        foreach ($_staff->commanding_staff as $key => $staff) {
            if(sizeof($staff->commanding_staff) > 0){
				if($staff->rfc != null){
					if($this->get_field == 'all_fields'){
						$this->staff_personal_intelisis[] = $staff;
					}else{
						$this->staff_personal_intelisis[] = $staff[$this->get_field];
					}
				}
                $this->get_staff($staff);

            }else{
				if($staff->rfc != null){
					if($this->get_field == 'all_fields'){
						$this->staff_personal_intelisis[] = $staff;
					}else{
						$this->staff_personal_intelisis[] = $staff[$this->get_field];
					}
					
				}
            }
        }
    }

}

