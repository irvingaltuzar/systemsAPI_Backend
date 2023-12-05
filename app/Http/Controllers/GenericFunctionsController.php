<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis;
use App\Models\DmiControlSignaturesBehalf;
use App\Repositories\GeneralFunctionsRepository;
use DateTime;
use Carbon\Carbon;
use PDF;
use App\Services\SendEmailService;
use App\Models\DmiControlProcedureValidation;


class GenericFunctionsController extends Controller
{


	public function __construct()
	{
        $this->middleware('guest',['only'=>'ShowLogin']);
	}

    public function listUsersSignaturesBehalf(Request $request){

		$list = DmiControlSignaturesBehalf::all();

		return ['success' => 1, 'data' => $list];
    }

	public function checkUserSignOnBehalf($_seccion,$_usuario){

		$is_valid = DmiControlSignaturesBehalf::where('seg_seccion_id',$_seccion)->where('behalf_usuario_ad',$_usuario)->get();

		if(sizeof($is_valid) > 0){
			return ['is_valid' => 1,'data'=> collect($is_valid)->pluck('usuario_ad')->toArray()];
		}else{
			return ['is_valid' => 0,'message' => 'El usuario no pude firmar en nombre de nadie.'];
		}

    }

	public function getTokenUserAdhoc(){
		$token = DmiControlProcedureValidation::where('key',"OtrosSistemas_token_adhoc-password")->first();
		if($token != null){
			return ["success" => 1, "data"=>$token->value];
		}else{
			return ["success" => 0, "data"=>null,"message"=>"No se encontro ningun token"];
		}
	}





}

