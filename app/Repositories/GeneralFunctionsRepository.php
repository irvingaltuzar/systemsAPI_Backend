<?php

namespace App\Repositories;

use App\Models\PersonalIntelisis;
use App\Models\DmiControlPlazaSustitution;
use App\Models\DmiControlAuthorizationSignature;
use Illuminate\Support\Facades\DB;

class GeneralFunctionsRepository
{

	public $previous_plaza_top;

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

        $get_director = PersonalIntelisis::with("get_higher")->where('usuario_ad',$_personal_intelisis_usuario_ad)->where('status','ALTA')->first();
		if($get_director->get_higher != null){
            $director = $this->haveTopPlaza($get_director);
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
            //return "Error al encontrar la plaza superior, contacta a soporte.";
        }
    }

    public function haveTopPlaza($_personal_intelisis){
        $top = $_personal_intelisis->get_higher;

		if($top != null){
			if(gettype(strpos(strtoupper($top->position_company_full),'DIRECTOR')) === "integer"){
				return $top;
			}else if($top->position_company_full == null){
				if($top->top_plaza_id == null && strpos(strtoupper($_personal_intelisis->position_company_full),'DIRECTOR') !== false ){
					return $top;
				}else{
					return null;
					//return "Error al encontrar la plaza superior, contacta a soporte.";
				}
			}else{
				$this->previous_plaza_top = $top;
				return $this->haveTopPlaza($top);
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


}

