<?php

namespace App\Services;

use App\Models\DmiRh\DmirhVacation;
use App\Models\DmiRh\DmirhWorkPermit;
use App\Models\FoodOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\DmiControlSignaturesBehalf;
use App\Models\PersonalIntelisis;

class SendEmailService
{
	public function test()
	{
		$mails = [
			'carlos.montejo@grupodmi.com.mx'
		];

		$collect = FoodOrder::first();

		dispatch(new \App\Jobs\SendEmailJob($mails, $collect, "Food"))->afterResponse();
	}

	public function notificationEmailRRHH($_data)
	{

		$mails = [$_data['to_email']];
		//$mails = ["eladio.perez@grupodmi.com.mx"];
		$data = $_data['data'];
		$module = $_data['module'];

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, $module))->afterResponse();
	}

	public function sendCancelMessage($data, $type)
	{

		$main_mail = $data->supplier['responsable']['email'] != null ? $data->supplier['responsable']['email'] : $data->second_mail;


		$mails = [
			'main_mails' => [
				$main_mail,
				'laura.faustino@grupodmi.com.mx',
				'teresa.campos@grupodmi.com.mx',
				'martin.soto@andares.com',
				'rodrigo.rondero@grupodmi.com.mx'
			],
			'second_mails' => [
				$data->second_mail
			]
		];

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, $type))->afterResponse();
	}

	public function sendApproveMessage($data, $type)
	{
		$mails = [
			'main_mails' => [
				$data->responsable->email,
				'laura.faustino@grupodmi.com.mx',
				'teresa.campos@grupodmi.com.mx',
				'martin.soto@andares.com',
				'rodrigo.rondero@grupodmi.com.mx'
			],
		];

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, $type))->afterResponse();
	}

	public function sendRemoveSuppierMessage($data, $type, $comment)
	{
		$user = Auth()->user()->personal_intelisis;

		$full_name = "$user->name $user->last_name";

		$mails = [
			'main_mails' => [
				$data->responsable->email,
				'laura.faustino@grupodmi.com.mx',
				'teresa.campos@grupodmi.com.mx',
				'martin.soto@andares.com',
				'rodrigo.rondero@grupodmi.com.mx'
			],
		];

		$data['comment'] = $comment;
		$data['removed_by'] = $full_name;


		dispatch(new \App\Jobs\SendEmailJob($mails, $data, $type))->afterResponse();
	}

	public function sendNextSigner($data, $type)
	{
		$email_user_sign="";
		$name_user_sign="";
		$solicitud = null;
		$sub_seccion = null;

		if($data->seg_seccion_id == 9){
			// Permisos
			$solicitud = DmirhWorkPermit::with('personal_intelisis')->find($data->origin_record_id);
			$sub_seccion = "work_permit";
		}else if($data->seg_seccion_id == 11){
			//Vacaciones
			$solicitud = DmirhVacation::with('personal_intelisis')->find($data->origin_record_id);
			$sub_seccion = "vacation";

		}


		// Se verifica si
		$sign_behalf = DmiControlSignaturesBehalf::where('usuario_ad',$data->personal_intelisis_usuario_ad)
													->where('seg_seccion_id',$data->seg_seccion_id)->first();
		if($sign_behalf != null){
			$personal_intelisis = PersonalIntelisis::where('usuario_ad',$sign_behalf->behalf_usuario_ad)
													->where('status','alta')->first();

			$name_user_sign = "{$personal_intelisis->name} {$personal_intelisis->last_name}";
			$email_user_sign = $personal_intelisis->email;

		}else{
			$name_user_sign = "{$data->personal_intelisis->name} {$data->personal_intelisis->last_name}";
			$email_user_sign = $data->personal_intelisis->email;
		}


		$data['owner_full_name'] = "{$solicitud->personal_intelisis->name} {$solicitud->personal_intelisis->last_name}";
		$data['name_user_sign'] = $name_user_sign;
		$data['sub_seccion'] = $sub_seccion;

		$mails = [
			$email_user_sign
			//'eladio.perez@grupodmi.com.mx'
		];

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, $type))->afterResponse();
	}

	public function newRequisitionNotification($data,$mails)
	{

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "new_requisition"))->afterResponse();
	}

	public function signRequisitionNotification($data,$mails)
	{

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "sign_requisition"))->afterResponse();
	}
	public function validateRequisitionNotification($data,$mails)
	{

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "validate_requisition"))->afterResponse();
	}

	public function cancelRequisitionNotification($data,$mails)
	{

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "cancel_requisition"))->afterResponse();
	}
	public function autorizeRequisitionNotification($data,$mails)
	{

		dispatch(new \App\Jobs\SendEmailJob($mails, $data, "autorize_requisition"))->afterResponse();
	}
}
