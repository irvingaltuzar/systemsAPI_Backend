<?php

namespace App\Http\Controllers\Alfa\PersonalRequisitions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis;
use App\Models\VwDmiPersonalPlaza;
use App\Models\DmiControlEmailDomain;
use App\Models\DmiControlProcess;
use App\Models\DmirhPersonalRequisition;
use App\Models\DmiBucketSignature;
use App\Models\DmiCatStatusRecruitment;
use App\Models\CatRequisitionsAdmin;
use App\Models\DmiControlSignaturesBehalfAudit;
use App\Models\DmiControlAuthorizationSignature;
use Carbon\Carbon;
use App\Models\File as MFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PDF;
use App\Services\SendEmailService;
use App\Services\IntelisisSenderService;
use App\Http\Controllers\NotificationCenterController;
use App\Models\DmiControlProcedureValidation;

class PersonalRequisitionController extends Controller
{
    private $sendEmail, $intelisisService;

	public function __construct(SendEmailService $sendEmail, IntelisisSenderService $intelisisService, NotificationCenterController $notificationCenter)
	{
		$this->sendEmail = $sendEmail;
		$this->intelisisService = $intelisisService;
        $this->notificationCenter= $notificationCenter;
	}
    protected function getCompany_Sucursal(){
        if(Auth::check()){
            $res= DB::Select("select * FROM vw_company_name");

            return response()->json($res, 200);
        }else{
            return response()->json(null, 500);

        }
    }
    protected function getAllBranch_Code(){
        if(Auth::check()){
            $res= DB::Select("select * FROM vw_branch_code");

            return response()->json($res, 200);
        }else{
            return response()->json(null, 500);

        }
    }
    public $salida = array();

    protected function getAllCommanding_staff(){
        if(Auth::check()){
            // $res = PersonalIntelisis::with('staffAll')->where("usuario_ad",Auth::user()->usuario)->where('status', '=', "ALTA")->first();
            $res = PersonalIntelisis::with('staffall')->where("usuario_ad",Auth::user()->usuario)->where('status', '!=', "BAJA")->get();

            // $res = PersonalIntelisis::with('commanding_staff_All')->where("usuario_ad","arturo.jara")->get();
            $this->encuentraParents($res,$salida,null);


              return response()->json($salida, 200);
        }else{
            return response()->json(null, 500);

        }
    }
    function encuentraParents($entrada, &$salida, $padre) {
        // para cada elemento del array
        // $salida[] =$entrada;
        foreach($entrada as $valor) {
            // añade una entrada al array de salida indicando su id y el de su padre
            if(isset($padre) ){
                $salida[] = $valor;
        }
        //     // si el elemento tiene children
            if (isset($valor["staffall"]) ) {
                // procesa los hijos recursivamente indicando el id del padre
               $this->encuentraParents($valor["staffall"], $salida, $valor["plaza_id"] );
            }
        }
    }

    protected function getAllCommanding_staff_Panel(Request $request){
        if(Auth::check()){
            $res = PersonalIntelisis::with('staffall')->where("usuario_ad",$request["user"])->where('status', '!=', "BAJA")->get();
            // $res = PersonalIntelisis::with('commanding_staff_All')->where("usuario_ad","arturo.jara")->get();
            $this->encuentraParents($res,$salida,null);
            // $res2= $this->recurPersonal($res);
              return response()->json($salida, 200);
            // return response()->json($res, 200);
        }else{
            return response()->json(null, 500);

        }
    }

    protected function getCommanding_staff(){
        if(Auth::check()){
            $res = PersonalIntelisis::with('commanding_staff')->where("usuario_ad",Auth::user()->usuario)->where("status","ALTA")->first();
            // $res = PersonalIntelisis::with('commanding_staff')->where("usuario_ad","arturo.jara")->first();

            return response()->json($res, 200);
        }else{
            return response()->json(null, 500);

        }
    }
    protected function getEmailDomain(){
        if(Auth::check()){
            $res = DmiControlEmailDomain::all();

            return response()->json($res, 200);
        }else{
            return response()->json(null, 500);

        }
    }

    protected function getSuperValidator(){
        if(Auth::check()){

            $res = DB::table("cat_requisitions_supervalidator")->where("responsable_user",Auth::user()->usuario)->first();
            if(isset($res)){
                return response()->json(true, 200);

            }else{
            return response()->json(null, 200);

            }
        }else{
            return response()->json(null, 500);

        }
    }
    protected function getStatusRecruitment(){
        if(Auth::check()){
            $res = DmiCatStatusRecruitment::all();

            return response()->json($res, 200);
        }else{
            return response()->json(null, 500);

        }
    }
    protected function getStaff_HigherConsulta(Request $req){
        if(Auth::check()){
            $arrsignatures=[];
            $signs = DmiBucketSignature::where('origin_record_id',$req->id_req)
            ->where('seg_seccion_id',12)->orderBy("order")->get();

            foreach ($signs as $sign) {
            $position="";

             $inf= PersonalIntelisis::where("usuario_ad",$sign["personal_intelisis_usuario_ad"])->where("status","ALTA")->first();

             if($inf==null){
                $inf= PersonalIntelisis::where("usuario_ad",$sign["personal_intelisis_usuario_ad"])->orderBy("id","desc")->first();
             }
             if(isset($inf)){
             if( $inf->position_company_full === null || $inf->position_company_full === ""){
                    $position=$inf["position_company"];
             }else{
                $position=$inf["position_company_full"];
             }
             if($sign["signed_date"]!= null || $sign["signed_date"]!= ""){
                $dSign= Carbon::parse($sign["signed_date"])->format('d-m-Y H:i:s');
             }else{
                $dSign="";
             }
                array_push($arrsignatures,(object)[
                    "id"=> $sign["id"],
                    "name"=>  $inf["name"]." ".$inf["last_name"],
                    "position_company"=> $position,
                    "user"=> $sign["personal_intelisis_usuario_ad"],
                    "Firma"=> $sign["status"],
                    "Firma_Digital"=>  Carbon::parse($sign["created_at"])->timestamp,
                    "signed_date"=> $dSign,
                    ]);
            }
        }
            return response()->json($arrsignatures, 200);
        }else{
            return response()->json(null, 500);

        }
    }
    public $last = array();
    //getSignaturesAuthorizations
    protected function  getStaff_Higher(Request $req){
        if(Auth::check()){
            $arrsignatures=[];
            $get_director = PersonalIntelisis::with('get_higher_cop')->where("usuario_ad",$req["user"])->where("status","ALTA")->first();
            $getplaza= DB::table('dmicontrol_authorization_signatures')
                        ->join('dmicontrol_process', 'dmicontrol_authorization_signatures.dmi_control_process_id', '=', 'dmicontrol_process.id')
                        ->where('dmicontrol_process.name',$req["type"])
                        ->select('plaza_id')
                        ->first();
        if($get_director!=null || $get_director!= ""){
            $get_Authorization = PersonalIntelisis::where('plaza_id',$getplaza->plaza_id)->where("status","ALTA")->first();
            //Si tiene firmas ya realizadas le mandamos la informacion completa
          //firma Solicitante
        //   return response()->json($get_director, 200);
                array_push($arrsignatures,(object)[
                    "name"=>  $get_director->name." ".$get_director->last_name,
                    "position_company"=> $get_director->position_company_full,
                    "user"=> $get_director->usuario_ad,

                    ]);
                    if($get_director->get_higher_cop->position_company_full == null){
                        $position= $get_director->get_higher_cop->position_company_full_plazas;
                    }else{
                        $position= $get_director->get_higher_cop->position_company_full;
                    }
                    //Firma Jefe directo
                    array_push($arrsignatures,(object)[
                        "name"=>  $get_director->get_higher_cop->name." ".$get_director->get_higher_cop->last_name,
                        "position_company"=> $position,
                        "user"=> $get_director->get_higher_cop->usuario_ad,
                        ]);

                        if($req["type"] ==='Nueva creacion'){
                             $this->haveTopPlaza($get_director,$last);
                        if($last!= null || $last!= ""){
                            array_push($arrsignatures,(object)[
                                "name"=> $last->name." ". $last->last_name,
                                "position_company"=>  $last->position_company_full_plazas,
                                "user"=>  $last->usuario_ad,
                                ]);
                            }
                    }
                }else{

                    return response()->json(['error'=> "Error al encontrar la plaza superior, contacta a soporte."], 200);

                }
                //Firma recursos Humanos
                        array_push($arrsignatures,(object)[
                            "name"=>  $get_Authorization->name." ".$get_Authorization->last_name,
                            "position_company"=> $get_Authorization->position_company_full,
                            "user"=> $get_Authorization->usuario_ad,
                            ]);
            return response()->json($arrsignatures, 200);
        }else{
            return response()->json(null, 500);
        }
    }
    function haveTopPlaza($_personal_intelisis,&$last){
        $last = $_personal_intelisis["get_higher_cop"];
        $top = $_personal_intelisis["get_higher_cop"];

        if(str_contains($top["position_company_full_plazas"], 'VP') ){
             return $last;

        }
        if($top["top_plaza_id"] == "") {
            return $last;

       }
        $this->haveTopPlaza($top,$last);

       }

    protected function addPersonalRequisition(Request $request){
        if(Auth::check()){
            $data=[];
            $requisition = new DmirhPersonalRequisition();
            if($request->file('file')){

                $uploaded_file = $request->file('file');
                $file_name = $uploaded_file->getClientOriginalName();
                $time= time();

                if( Storage::disk("Requisitions")->putFileAs("/", $uploaded_file, "Desc_Pues_".$time."_".$file_name)){
                  $requisition->file= "Desc_Pues_".$time."_".$file_name;
                }
              }
              $pers=PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();
              $arrResources= array_map('intval', explode(",",$request["resourcesList"]));
            $requisition->type=$request["type"];
            $requisition->department=$request["deparment"];
            $requisition->level_position=$request["level"];
            $requisition->date=Carbon::parse($request["date"])->format('Y-m-d H:i:s');;
            $requisition->vacancy=$request["vacancy"];
            $requisition->num_vacancy=$request["num_vacancy"];
            $requisition->personal_substitution=$request["personal_substitution"];
            $requisition->reason_replacement=$request["reason_replacement"];
            $requisition->type_vacancy=$request["type_vacancy"];
            $requisition->time_travel=$request["time_travel"];
            $requisition->days_travel=$request["days_travel"];
            $requisition->temp_reason=$request["personal_temporary"];
            $requisition->days_temp_reason=$request->temp_days;
            $requisition->salary=$request["salary"];
            $requisition->estimate=$request["estimate"];
            $requisition->user=auth()->user()->usuario;
            $requisition->email_domain_id= $request["email_domain"];
            $requisition->resources= $arrResources;
            $requisition->software_aditional=$request["software_aditional"];
            $requisition->personal_location=$request["personal_location"];
            $requisition->company_name=$request["company_name"];
            $requisition->branch_code=$request["branch_code"];
            $requisition->status="validacion";
            $requisition->plaza_id=$request["plaza_id"];
            $requisition->user_plaza_id=$pers->plaza_id;

            $requisition->save();
            $lastid=DB::getPdo()->lastInsertId();
            $req= DmirhPersonalRequisition::with("personal_intelisis")->find($lastid);
            $data["data"] = $req;


        //     if($req->personal_intelisis->location=="CORPORATIVO" || $req->personal_intelisis->location=="FUNDACION" || $req->personal_intelisis->location=="LOS ROBLES" || $req->personal_intelisis->location=="MAYAKOBA"){
        //     $coord= PersonalIntelisis::where("location","CORPORATIVO")->where("position_company","COORDINADOR")
        //     ->where("deparment","RECURSOS HUMANOS")->where("status","ALTA")->select("email")->get();
        //  }else{
        //     $coord= PersonalIntelisis::where("location",$req->personal_intelisis->location)->where("position_company","COORDINADOR")
        //     ->where("deparment","RECURSOS HUMANOS")->where("status","ALTA")->select("email")->get();
        // }
        $res = DB::table("dmicontrol_signatures_behalves")->where("dmi_control_process_id",2)
        ->select('behalf_usuario_ad');
        $coord= PersonalIntelisis::where("status","ALTA")->whereIn("usuario_ad",$res)->select("email")->get();
        foreach ($coord as $row) {
            $mails[]= $row->email;
        }

            $order=1;
            $array=json_decode($request["personal_autorizations"], TRUE);
            foreach ( $array as $value) {
            $signature= new DmiBucketSignature();
            $signature->origin_record_id= $lastid;
            $signature->seg_seccion_id=12;
            $signature->personal_intelisis_usuario_ad=$value["user"];
            $signature->status=null;
            $signature->order=$order;
            $signature->save();
            $order++;
            }
            // $mails[]= "irving.altuzar@grupodmi.com.mx";
            $this->sendEmail->newRequisitionNotification($data, $mails);

            return response()->json(['success'=>'Se ha creado solicitud de requisición.'],200);
        }else{
            return response()->json(null, 500);

        }
    }
    protected function updatePersonalRequisition(Request $request){

            $requisition = DmirhPersonalRequisition::find($request->id);
            try {
                if($request->file('file')){

                    if($requisition->file){
                        $nam= $requisition->file;
                        $requisition->file='';
                        unlink("../Storage/app/Requisitions/$nam");
                        }
                    $uploaded_file = $request->file('file');
                    $file_name = $uploaded_file->getClientOriginalName();
                    // $sizefile= $uploaded_file->getSize();
                    // $original_ext = $uploaded_file->getClientOriginalExtension();
                    // $type = $file->getType($original_ext);
                    // $fullname=$request["titulo"].'.'. $original_ext;
                    $time= time();

                    if (Storage::disk("Requisitions")->exists($file_name)) {

                        unlink("../Storage/app/Requisitions/$file_name");
                        if( Storage::disk("Requisitions")->putFileAs("/", $uploaded_file, "Desc_Pues_".$time."_".$file_name)){
                            $requisition->file= "Desc_Pues_".$time."_".$file_name;
                          }
                    }else{
                        if( Storage::disk("Requisitions")->putFileAs("/", $uploaded_file, "Desc_Pues_".$time."_".$file_name)){
                            $requisition->file= "Desc_Pues_".$time."_".$file_name;
                          }
                    }

                }else{
                    // if($requisition->file != null || $requisition->file != ""){
                    // $nam= $requisition->file;
                    // $requisition->file='';
                    // unlink("../Storage/app/Requisitions/$nam");
                    // }
                }
                // else if($request['file'] != ""){
                //  $orig=$request['file_name'];
                //  $original_ext=explode(".",$request["file_name"]);
                //  $fullname= $request["titulo"].'.'.$original_ext[1];
                //  if($request["file_name"] != $fullname){
                //     rename("../Storage/app/$privacidad/$orig", "../Storage/app/$privacidad/$fullname");
                //  }
                // }
            } catch (\Throwable $th) {
                throw $th;
            }

            // if($request->file('file')){

            //     $uploaded_file = $request->file('file');
            //     $file_name = $uploaded_file->getClientOriginalName();

            //     if( Storage::disk("Requisitions")->putFileAs("/", $uploaded_file, "Desc_Pues_".$time."_".$file_name)){
            //       $requisition->file= "Desc_Pues_".$time."_".$file_name;
            //     }
            //   }
              $arrResources= array_map('intval', explode(",",$request["resourcesList"]));
            //   return $arrResources;
            $requisition->type=$request["type"];
            $requisition->department=$request["deparment"];
            $requisition->level_position=$request["level"];
            $requisition->date=Carbon::parse($request["date"])->format('Y-m-d H:i:s');;
            $requisition->vacancy=$request["vacancy"];
            $requisition->num_vacancy=$request["num_vacancy"];
            $requisition->reason_replacement=$request["reason_replacement"];
            $requisition->personal_substitution=$request["personal_substitution"];
            $requisition->type_vacancy=$request["type_vacancy"];
            $requisition->time_travel=$request["time_travel"];
            $requisition->days_travel=$request["days_travel"];
            $requisition->temp_reason=$request["temp_reason"];
            $requisition->days_temp_reason=$request->temp_days;
            $requisition->salary=$request["salary"];
            $requisition->estimate=$request["estimate"];
            // $requisition->user=$request["user"];
            $requisition->email_domain_id= $request["email_domain"];
            // $requisition->resources= $request["resourcesList"];
            $requisition->resources= $arrResources;
            $requisition->software_aditional=$request["software_aditional"];
            $requisition->personal_location=$request["personal_location"];
            $requisition->company_name=$request["company_name"];
            $requisition->branch_code=$request["branch_code"];
            $requisition->plaza_id=$request["plaza_id"];
            // $requisition->status="Validacion";
            $requisition->save();

            return response()->json(['success'=>'Se ha actualizado solicitud de requisición.'],200);

    }
    protected function ValidatePersonalRequisition(Request $request){

        $data=[];
            $requisition = DmirhPersonalRequisition::find($request->id);

            $requisition->status="recaudar firmas";
            $requisition->status_recruitment_id=1;
            $requisition->date_validation_rh=Carbon::now();
            $requisition->save();

            $signatures = DmiBucketSignature::where('origin_record_id',$request->id)
            ->where('seg_seccion_id',12)->orderBy("order")
            ->first();

            $signature= DmiBucketSignature::find($signatures->id);
            $signature->status="pendiente";
            $data["data"]=$requisition;
            $mail= PersonalIntelisis::where("usuario_ad",$requisition->user)->where("status","ALTA")->first();
            if( $mail->email!= "" ||  $mail->email !=null){
                $mails[]= $mail->email;
                $this->sendEmail->validateRequisitionNotification($data, $mails);
            }
            $signature->save();



            return response()->json(['success'=>'Se ha validado solicitud de requisición.'],200);

    }
    protected function CancelPersonalRequisition(Request $request){

            $requisition = DmirhPersonalRequisition::find($request->id);

            $requisition->status="cancelada";
            $requisition->status_recruitment_id=1;
            $data["data"]=$requisition;
            $mail= PersonalIntelisis::where("usuario_ad",$requisition->user)->where("status","ALTA")->first();
            if( $mail->email!= "" ||  $mail->email !=null){
                $mails[]= $mail->email;
                $this->sendEmail->cancelRequisitionNotification($data, $mails);
                $this->notificationCenter->addNotificationCenter(
                    $requisition->user,
                    "Solicitud Requisición cancelada",
                "La requisición ".$requisition->id." no ha sido aprobada, consulta para mas detalles...",
                "notification",
                "MisRequisiciones",
                "cancel_request","media");
            }
            $requisition->save();

            return response()->json(['success'=>'Se ha cancelado solicitud de requisición.'],200);

    }

    protected function AutorizeRequisition(Request $request){


        $signatures = DmiBucketSignature::where('origin_record_id',$request->id)
        ->where('seg_seccion_id',12)
        ->get();

                foreach($signatures as $sign) {
                    if($sign->status==null || $sign->status=="" || $sign->status=="pendiente"){
                        $espSign= DmiBucketSignature::where('seg_seccion_id',12)
                        ->where('personal_intelisis_usuario_ad', $sign->personal_intelisis_usuario_ad)
                        ->where("status","<>","firmado")->where("origin_record_id",$request->id)->first();
                        if(isset($espSign) && $espSign!= null){
                            $sign=$espSign;
                            $signaudit= new DmiControlSignaturesBehalfAudit();
                            $signaudit->origin_record_id=$request->id;
                            $signaudit->seg_seccion_id=12;
                            $signaudit->sign_behalf_usuario_ad=auth()->user()->usuario;
                            $signaudit->save();
                        }
                    $sign->signed_date = Carbon::now();
                    $sign->status = "firmado";
                    $sign->save();
                    }
                }
                $hoy = date("Y-n-j");
                $permit =DmirhPersonalRequisition::find($request->id);

                //Tipo de Vacantes con Dias Habiles definidos en cada uno
                if($permit->level_position == "Operativo"  || $permit->level_position == "OPERATIVO"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 17){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Administrativo" || $permit->level_position == "ADMINISTRATIVO"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 21){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Gerente" || $permit->level_position == "GERENTE"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 36){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Directivo" || $permit->level_position == "DIRECTIVO"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 36){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Promocion Interna" || $permit->level_position == "PROMOCION INTERNA"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 31){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }
                $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
                $permit->status = "autorizada";
                $permit->status_recruitment_id=1;
                $permit->date_validation_rh = Carbon::now();
                $permit->date_received_rh = Carbon::now();
                $permit->date_estimate_coverage = $nuevafecha;
                $permit->save();
                $permit = DmirhPersonalRequisition::find($request->id);
                $data["data"]=$permit;
                $mail= PersonalIntelisis::where("usuario_ad",$permit->user)->where("status","ALTA")->first();
                if( $mail->email!= "" ||  $mail->email !=null){
                    $mails[]= $mail->email;
                    $this->sendEmail->autorizeRequisitionNotification($data, $mails);
                    $this->notificationCenter->addNotificationCenter(
                        $permit->user,
                        "Solicitud Requisición aprobada",
                    "La requisición ".$permit->id." ha sido aprobada, consulta para mas detalles...",
                    "notification",
                    "MisRequisiciones",
                    "approved_request","media");
                }

                $data = DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis','dmi_personal_substitution'])->find($request->id);
                $signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$request->id)->where('seg_seccion_id',12)->orderBy("order","ASC")->orderBy("id","ASC")->get();
                $signAudit= DmiControlSignaturesBehalfAudit::with('personal_intelisis_requisition')->where("origin_record_id",$request->id)->get();// $data["signatures"]=$signatures;
                $this->generarPDFPersonalRequisition($data, $signatures,$signAudit);

        return response()->json(['success'=>'Se ha autorizado solicitud de requisición.'],200);

}

    protected function SignPersonalRequisition(Request $request){

        $res = DB::table("dmicontrol_signatures_behalves")->where("behalf_usuario_ad",auth()->user()->usuario)
        ->select('usuario_ad');
        if(isset($res)){
        $espSign= DmiBucketSignature::where('seg_seccion_id',12)
        ->whereIn('personal_intelisis_usuario_ad', $res)
        ->where(function($query) {
            $query->where("status","pendiente");
        })->where("origin_record_id",$request->id)->first();
        }
        if(isset($espSign) && $espSign!= null){
            $sign=$espSign;
            $signaudit= new DmiControlSignaturesBehalfAudit();
            $signaudit->origin_record_id=$request->id;
            $signaudit->seg_seccion_id=12;
            $signaudit->sign_behalf_usuario_ad=auth()->user()->usuario;
            $signaudit->save();
        }else{
            $sign = DmiBucketSignature::where('origin_record_id',$request->id)
            ->where('seg_seccion_id',12)->where("status","pendiente")
            ->where('personal_intelisis_usuario_ad',$request->user)
            ->first();
        }

        if($sign != null){
            $status = null;
            if($request->status == "true"){
                $status = "firmado";
            }else if($request->status == "false"){
                $status = "rechazada";
            }

            if($status != null){
                $sign->signed_date = Carbon::now();
                $sign->status = $status;
                $sign->save();

                if($sign != null){
                    if($status == "firmado"){
                        // Se cambia el estatus a pendiente de la siguiente firma y si es la ultima
                        // Se cambia el estatus al permiso a *autorizado*

                        $signatures = DmiBucketSignature::where('origin_record_id',$request->id)
                                        ->where('seg_seccion_id',12)
                                        ->get();

                        $end_sign = sizeof($signatures)-1;
                        $active_sign = false;
        foreach ($signatures as $key => $item) {
            if(($item->status == "firmado" || $item->status == "completada")){
                if($end_sign == $key){
                    // Se verifica si se completaron todas las firmas y se cambia el estatus al permiso en caso de estar todas las firmas
                    $verify_signatures = DmiBucketSignature::where('origin_record_id',$request->id)
                        ->where('seg_seccion_id',12)
                        ->get();

                    $cont_sign = 0;
                    foreach($verify_signatures as $key => $v_item){
                        if(($v_item->status == "firmado" || $v_item->status == "completada") && $v_item->signed_date != null){
                            $cont_sign++;
                        }
                    }

        if($cont_sign == sizeof($verify_signatures)){
                 $hoy = date("Y-n-j");
                $permit =DmirhPersonalRequisition::find($request->id);

                //Tipo de Vacantes con Dias Habiles definidos en cada uno
                if($permit->level_position == "Operativo"  || $permit->level_position == "OPERATIVO"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 17){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Administrativo" || $permit->level_position == "ADMINISTRATIVO"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 21){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Gerente" || $permit->level_position == "GERENTE"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 36){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Directivo" || $permit->level_position == "DIRECTIVO"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 36){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }else if($permit->level_position == "Promocion Interna" || $permit->level_position == "PROMOCION INTERNA"){
                    $dia = date("w", strtotime($hoy));
                    $suma = 1;
                    $a = 1;
                    while($a < 31){
                        $nuevafecha = strtotime ( "+$suma day" , strtotime ( $hoy ) ) ;
                        $dia2 = date("Y-n-j", $nuevafecha);
                        $dia = date("w", strtotime($dia2));
                        if($dia != 0 && $dia != 6){
                            $a++;
                        }
                        $suma++;
                    }
                }
                $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
                $permit->status = "autorizada";
                $permit->date_received_rh = Carbon::now();
                $permit->date_estimate_coverage = $nuevafecha;
                $permit->save();

                $requisition = DmirhPersonalRequisition::find($request->id);
                $data["data"]=$requisition;
                $mail= PersonalIntelisis::where("usuario_ad",$requisition->user)->where("status","ALTA")->first();
                if( $mail->email!= "" ||  $mail->email !=null){
                    $mails[]= $mail->email;
                    $this->sendEmail->autorizeRequisitionNotification($data, $mails);
                    $this->notificationCenter->addNotificationCenter(
                        $requisition->user,
                        "Solicitud Requisición aprobada",
                    "La requisición ".$requisition->id." ha sido aprobada, consulta para mas detalles...",
                    "notification",
                    "MisRequisiciones",
                    "approved_request","media");
                }
                                        $data = DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis','dmi_personal_substitution'])->find($request->id);
                                        $signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$request->id)->where('seg_seccion_id',12)->orderBy("order","ASC")->orderBy("id","ASC")->get();
                                        $signAudit= DmiControlSignaturesBehalfAudit::with('personal_intelisis')->where("origin_record_id",$request->id)->get();// $data["signatures"]=$signatures;
                                        $this->generarPDFPersonalRequisition($data, $signatures, $signAudit);
                                    }

                                }else{
                                    $active_sign = true;
                                }
                            }else if($active_sign == true && $item->status == null){
                                //Activamos la siguiente firma
                                $item->status = "pendiente";
                                $item->save();
                                $requisition = DmirhPersonalRequisition::find($request->id);
                                $data["data"]=$requisition;
                                $mail= PersonalIntelisis::where("usuario_ad",$item->personal_intelisis_usuario_ad)->where("status","ALTA")->first();
                                if( $mail->email!= "" ||  $mail->email !=null){
                                    $mails[]= $mail->email;
                                    // $mails[]= "irving.altuzar@grupodmi.com.mx";
                                    $this->sendEmail->signRequisitionNotification($data, $mails);
                                    $this->notificationCenter->addNotificationCenter(
                                        $item->personal_intelisis_usuario_ad,
                                        "Solicitud Pendiente Firma",
                                    "La requisición ".$requisition->id." necesita de tu firma para su seguimiento",
                                    "notification",
                                    "authorisations",
                                    "sign_request","media");
                                }
                                $active_sign = false;
                            }

                        }

                    }else if($status == "rechazada"){
                        // Se eliminan cancelan las demas firmas
                        $requisition = DmirhPersonalRequisition::find($request->id);
                        $requisition->status= 'rechazada';
                        $data["data"]=$requisition;
                        $mail= PersonalIntelisis::where("usuario_ad",$requisition->user)->where("status","ALTA")->first();
                        if( $mail->email!= "" ||  $mail->email !=null){
                            $mails[]= $mail->email;
                            $this->sendEmail->cancelRequisitionNotification($data, $mails);
                            $this->notificationCenter->addNotificationCenter(
                                $requisition->user,
                                "Solicitud Requisición cancelada",
                            "La requisición ".$requisition->id." no ha sido aprobada, consulta para mas detalles...",
                            "notification",
                            "MisRequisiciones",
                            "cancel_request","media");
                        }
                        $requisition->save();
                    }
                    $work_permit = DmirhPersonalRequisition::find($request->id);
                    return ['success' => "Se ha firmado Solicitud", 'data'=>$work_permit, "action" => $status];
                }
            }else{
                return ['success' => 0, 'message'=> 'El estatus no enviado no es correcto.'];
            }


        }else{
            return ['success' => 0, 'message'=> 'No se encontró la firma solicitada.'];
        }

        return response()->json(['success'=>'Se ha cancelado solicitud de requisición.'],200);

}
    protected function getRequisitionValidatebyId(Request $request){
        if(Auth::check()){
        $res= DmirhPersonalRequisition::Find($request["id"]);

        return response()->json($res, 200);
    }else{
        return response()->json(null, 500);

    }
    }

    protected function getPersonalRequisitionValidation(){
        if(Auth::check()){

            //is Admin
            $adm= CatRequisitionsAdmin::where("responsable_user",auth()->user()->usuario)->first();

            if(isset($adm) || $adm!= ""){
                $res=DmirhPersonalRequisition::with('dmi_control_email_domain')->where("status","Validacion")
                ->orderBy("date","desc")->orderBy("id","desc")->get();
            }else{


           $loc= PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();
           if($loc->location=="CORPORATIVO"){
            $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','personal_intelisis'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
            ->select('dmirh_personal_requisition.*')
            ->where('personal_intelisis.status','ALTA')
            ->where("dmirh_personal_requisition.status","validacion")
            ->where(function($query) {
                $query->where("personal_intelisis.location","CORPORATIVO")
                ->orWhere("personal_intelisis.location","FUNDACION")
                ->orWhere("personal_intelisis.location","LOS ROBLES")
                ->orWhere("personal_intelisis.location","MAYAKOBA");
            })
            ->orderBy("dmirh_personal_requisition.date","desc")->get();
           }else{
            //Se busca a que ubicaciones tiene acceso la coordinación
            $locations = $this->getCoordinadoraRRHHLocation();
            if($locations == null){
                $locations = [$loc->location];
            }

            $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','personal_intelisis'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
            ->select('dmirh_personal_requisition.*')
            ->where('personal_intelisis.status','ALTA')
            ->where("dmirh_personal_requisition.status","validacion")
            ->whereIn('personal_intelisis.location',$locations)
            ->orderBy("dmirh_personal_requisition.date","desc")->get();
           }

        }


        // $array= $res;
        // $array2=[];
        // foreach ($res as $key => $value) {
        //     if($value->file!= null or $value->file!= ""){
        //         $file= Storage::disk("Requisitions")->url($value->file);
        //         $array[$key]['url']=$file;
        //     }else{
        //         $array[$key]['url']=null;
        //     }

        // }

        return response()->json($res, 200);
    }else{
        return response()->json(null, 500);

    }
    }
    protected function getConsultaPersonalRequisitions(Request $request){
        if(Auth::check()){

            $pagina = $request["pagina"];
            $limite = $request["limite"];

             //is Admin
             $adm= CatRequisitionsAdmin::where("responsable_user",auth()->user()->usuario)->first();

             if(isset($adm) || $adm!= ""){

        $res=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])
        ->where(function($query) {
            $query->where("dmirh_personal_requisition.status","recaudar firmas")
            ->orWhere("dmirh_personal_requisition.status","autorizada");
        })
        ->orderBy("date","desc")->orderBy("id","desc")->Paginate( $perPage =  $limite, $columns = ['*'], $pageName = 'page',$page =$pagina);

             }else{


            $loc= PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->first();
            if($loc->location=="CORPORATIVO"){
             $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
             ->select('dmirh_personal_requisition.*')
             ->where('personal_intelisis.status','ALTA')
             ->where(function($query) {
                $query->where("dmirh_personal_requisition.status","recaudar firmas")
                ->orWhere("dmirh_personal_requisition.status","autorizada");
            })
             ->where(function($query) {
                $query->where("personal_intelisis.location","CORPORATIVO")
                ->orWhere("personal_intelisis.location","FUNDACION")
                ->orWhere("personal_intelisis.location","LOS ROBLES")
                ->orWhere("personal_intelisis.location","MAYAKOBA");
             })
             ->orderBy("dmirh_personal_requisition.date","desc")->Paginate( $perPage =  $limite, $columns = ['*'], $pageName = 'page',$page =$pagina);
            }else{
                //Se busca a que ubicaciones tiene acceso la coordinación
                $locations = $this->getCoordinadoraRRHHLocation();
                if($locations == null){
                    $locations = [$loc->location];
                }

                $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
                ->select('dmirh_personal_requisition.*')
                ->where('personal_intelisis.status','ALTA')
                ->where(function($query) {
                    $query->where("dmirh_personal_requisition.status","recaudar firmas")
                    ->orWhere("dmirh_personal_requisition.status","autorizada");
                })
                ->whereIn('personal_intelisis.location',$locations)
                ->orderBy("dmirh_personal_requisition.date","desc")->Paginate( $perPage =  $limite, $columns = ['*'], $pageName = 'page',$page =$pagina);
            }

         }

        return response()->json($res, 200);
    }else{
        return response()->json(null, 500);

    }
    }

    protected function getAutRechPersonalRequisitions(Request $request){
        if(Auth::check()){

            $pagina = $request["pagina"];
            $limite = $request["limite"];

                //is Admin
                $adm= CatRequisitionsAdmin::where("responsable_user",auth()->user()->usuario)->first();

                if(isset($adm) || $adm!= ""){

            $res=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->where(function($query) {
                $query->where("status","autorizada")
                ->orWhere("status","cancelada")
                ->orWhere("dmirh_personal_requisition.status","rechazada");
            })
            ->orderBy("date","desc")->orderBy("id","desc")->Paginate( $perPage =  $limite, $columns = ['*'], $pageName = 'page',$page =$pagina);

                }else{


               $loc= PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();
               if($loc->location=="CORPORATIVO"){
                $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
                ->select('dmirh_personal_requisition.*')
                ->where('personal_intelisis.status','ALTA')
                ->where(function($query) {
                    $query->where("dmirh_personal_requisition.status","autorizada")
                    ->orWhere("dmirh_personal_requisition.status","cancelada")
                    ->orWhere("dmirh_personal_requisition.status","rechazada");
                })
                ->where(function($query) {
                    $query->where("personal_intelisis.location","CORPORATIVO")
                    ->orWhere("personal_intelisis.location","FUNDACION")
                    ->orWhere("personal_intelisis.location","LOS ROBLES")
                    ->orWhere("personal_intelisis.location","MAYAKOBA");
                })
                ->orderBy("dmirh_personal_requisition.date","desc")->Paginate( $perPage =  $limite, $columns = ['*'], $pageName = 'page',$page =$pagina);
               }else{
                //Se busca a que ubicaciones tiene acceso la coordinación
                $locations = $this->getCoordinadoraRRHHLocation();
                if($locations == null){
                    $locations = [$loc->location];
                }

                $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
                ->select('dmirh_personal_requisition.*')
                ->where('personal_intelisis.status','ALTA')
                ->where(function($query) {
                    $query->where("dmirh_personal_requisition.status","autorizada")
                    ->orWhere("dmirh_personal_requisition.status","cancelada")
                    ->orWhere("dmirh_personal_requisition.status","rechazada");
                })
                ->whereIn('personal_intelisis.location',$locations)
                ->orderBy("dmirh_personal_requisition.date","desc")->Paginate( $perPage =  $limite, $columns = ['*'], $pageName = 'page',$page =$pagina);
               }

            }


        return response()->json($res, 200);
    }else{
        return response()->json(null, 500);

    }
    }
    public function fetchRequisitionsConsulta(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


               //is Admin
               $adm= CatRequisitionsAdmin::where("responsable_user",auth()->user()->usuario)->first();

               if(isset($adm) || $adm!= ""){

           $res=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])
           ->where('dmirh_personal_requisition.id', 'LIKE', '%'.$search.'%')
           ->orWhere('dmirh_personal_requisition.date', 'LIKE', '%'.$search.'%')
           ->orWhere('dmirh_personal_requisition.type', 'LIKE', '%'.$search.'%')
           ->orWhere('dmirh_personal_requisition.user', 'LIKE', '%'.$search.'%')
           ->orWhere('dmirh_personal_requisition.personal_location', 'LIKE', '%'.$search.'%')
           ->orWhere('dmirh_personal_requisition.vacancy', 'LIKE', '%'.$search.'%')
           ->orWhere('dmirh_personal_requisition.date_estimate_coverage', 'LIKE', '%'.$search.'%')
           ->orWhere('dmirh_personal_requisition.status', 'LIKE', '%'.$search.'%')
           ->where(function($query) {
            $query->where("dmirh_personal_requisition.status","recaudar firmas")
            ->orWhere("dmirh_personal_requisition.status","autorizada");
        })
           ->orderBy("date","desc")->orderBy("id","desc")->Paginate(100);

               }else{

              $loc= PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();
              if($loc->location=="CORPORATIVO"){
               $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
               ->select('dmirh_personal_requisition.*')
               ->where('personal_intelisis.status','ALTA')
               ->where(function($query) use($search) {
                $query->where('dmirh_personal_requisition.id', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.type', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.user', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.personal_location', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.vacancy', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date_estimate_coverage', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.status', 'LIKE', '%'.$search.'%');
            })
               ->where(function($query) {
                $query->where("dmirh_personal_requisition.status","recaudar firmas")
                ->orWhere("dmirh_personal_requisition.status","autorizada");
            })
               ->where(function($query) {
                   $query->where("personal_intelisis.location","CORPORATIVO")
                   ->orWhere("personal_intelisis.location","FUNDACION")
                   ->orWhere("personal_intelisis.location","LOS ROBLES")
                   ->orWhere("personal_intelisis.location","MAYAKOBA");
               })
               ->orderBy("dmirh_personal_requisition.date","desc")->Paginate(100);
              }else{
               //Se busca a que ubicaciones tiene acceso la coordinación
               $locations = $this->getCoordinadoraRRHHLocation();
               if($locations == null){
                   $locations = [$loc->location];
               }
                
               $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
               ->select('dmirh_personal_requisition.*')
               ->where('personal_intelisis.status','ALTA')
               ->where(function($query) use($search) {
                $query->where('dmirh_personal_requisition.id', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.type', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.user', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.personal_location', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.vacancy', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date_estimate_coverage', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.status', 'LIKE', '%'.$search.'%');
            })
               ->where(function($query) {
                $query->where("dmirh_personal_requisition.status","recaudar firmas")
                ->orWhere("dmirh_personal_requisition.status","autorizada");
            })
                ->whereIn('personal_intelisis.location',$locations)
               ->orderBy("dmirh_personal_requisition.date","desc")->Paginate(100);
              }

           }
        

        return response()->json( $res, 200); 
	}
    public function fetchRequisitionsReclutamiento(Request $request)
	{
        $order_by = isset($request->order_by) ? $request->order_by : 'asc';

        $limit = (isset($request->limit) && $request->limit > 0) ? $request->limit : '20';

        $search = isset($request->search) ? $request->search : '';


               //is Admin
               $adm= CatRequisitionsAdmin::where("responsable_user",auth()->user()->usuario)->first();

               if(isset($adm) || $adm!= ""){

           $res=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])
           ->where('id', 'LIKE', '%'.$search.'%')
           ->orWhere('date', 'LIKE', '%'.$search.'%')
           ->orWhere('type', 'LIKE', '%'.$search.'%')
           ->orWhere('user', 'LIKE', '%'.$search.'%')
           ->orWhere('personal_location', 'LIKE', '%'.$search.'%')
           ->orWhere('vacancy', 'LIKE', '%'.$search.'%')
           ->orWhere('date_estimate_coverage', 'LIKE', '%'.$search.'%')
           ->orWhere('status', 'LIKE', '%'.$search.'%')
           ->where(function($query) {
               $query->where("status","autorizada")
               ->orWhere("status","cancelada")
               ->orWhere("dmirh_personal_requisition.status","rechazada");
           })
           ->orderBy("date","desc")->orderBy("id","desc")->Paginate(100);

               }else{

              $loc= PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();
              if($loc->location=="CORPORATIVO"){
               $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
               ->select('dmirh_personal_requisition.*')
               ->where('personal_intelisis.status','ALTA')
               ->where(function($query) use($search) {
                $query->where('dmirh_personal_requisition.id', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.type', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.user', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.personal_location', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.vacancy', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date_estimate_coverage', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.status', 'LIKE', '%'.$search.'%');
            })
               ->where(function($query) {
                   $query->where("dmirh_personal_requisition.status","autorizada")
                   ->orWhere("dmirh_personal_requisition.status","cancelada")
                   ->orWhere("dmirh_personal_requisition.status","rechazada");
               })
               ->where(function($query) {
                   $query->where("personal_intelisis.location","CORPORATIVO")
                   ->orWhere("personal_intelisis.location","FUNDACION")
                   ->orWhere("personal_intelisis.location","LOS ROBLES")
                   ->orWhere("personal_intelisis.location","MAYAKOBA");
               })
               ->orderBy("dmirh_personal_requisition.date","desc")->Paginate(100);
              }else{
                //Se busca a que ubicaciones tiene acceso la coordinación
                $locations = $this->getCoordinadoraRRHHLocation();
                if($locations == null){
                    $locations = [$loc->location];
                }

               $res= DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->join('personal_intelisis', 'personal_intelisis.usuario_ad', '=', 'dmirh_personal_requisition.user')
               ->select('dmirh_personal_requisition.*')
               ->where('personal_intelisis.status','ALTA')
               ->where(function($query) use($search) {
                $query->where('dmirh_personal_requisition.id', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.type', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.user', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.personal_location', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.vacancy', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.date_estimate_coverage', 'LIKE', '%'.$search.'%')
               ->orWhere('dmirh_personal_requisition.status', 'LIKE', '%'.$search.'%');
            })
               ->where(function($query) {
                   $query->where("dmirh_personal_requisition.status","autorizada")
                   ->orWhere("dmirh_personal_requisition.status","cancelada")
                   ->orWhere("dmirh_personal_requisition.status","rechazada");
               })
               ->whereIn('personal_intelisis.location',$locations)
               ->orderBy("dmirh_personal_requisition.date","desc")->Paginate(100);
              }

           }
        

        return response()->json( $res, 200); 
	}
    protected function getMyPersonalRequisitions(){
        if(Auth::check()){
        $pers=PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();
        $res=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->where("user_plaza_id",$pers->plaza_id)->get();
        $array= $res;

        foreach ($res as $key => $value) {
            $sign = DmiBucketSignature::where('origin_record_id',$value->id)
            ->where('seg_seccion_id',12)
            ->where('personal_intelisis_usuario_ad',auth()->user()->usuario)
            ->first();
            if( $sign!=null){
            $array[$key]['sign_status']= $sign->status;
            }
        }


        foreach ($res as $key => $value) {
            if($value->file!= null or $value->file!= ""){
                $file= Storage::disk("Requisitions")->url($value->file);
                $array[$key]['url']=$file;
            }else{
                $array[$key]['url']=null;

            }
            // if($value->document!= null or $value->document!= ""){
            //     $urlfile= Storage::disk("Requisitions")->url($value->document);
            //     $array[$key]['urlDocument']=$urlfile;
            // }else{
            //     $array[$key]['urlDocument']=null;

            // }
        }
        return response()->json( $array, 200);

    }else{
        return response()->json(null, 500);


    }

}
        protected function getRequisitionsMyPersonal(){
            if(Auth::check()){
                $array=[];
            $staff = PersonalIntelisis::with('commanding_staff')->where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();

            foreach($staff->commanding_staff as $k => $val){


            $res=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])->where("user",$val->usuario_ad)->orderBy("date","desc")->orderBy("id","desc")->get();

            if(count($res)>0){
                    foreach ($res as $key => $value) {
                        array_push($array,$value);

                            $sign = DmiBucketSignature::where('origin_record_id',$value->id)
                            ->where('seg_seccion_id',12)
                            ->where('personal_intelisis_usuario_ad',auth()->user()->usuario)
                            ->first();
                        if($sign!= null){
                            $array[$key]['sign_status'] = $sign->status;
                        }
                        if($value->file!= null or $value->file!= ""){
                            $file= Storage::disk("Requisitions")->url($value->file);
                            $array[$key]['url']=$file;
                        }else{
                            $array[$key]['url']=null;

                        }
                        // if($value->document!= null or $value->document!= ""){
                        //     $urlfile= Storage::disk("Requisitions")->url($value->document);
                        //     $array[$key]['urlDocument']=$urlfile;
                        // }else{
                        //     $array[$key]['urlDocument']=null;

                        // }
                    }
            }
        }
        return response()->json($array, 200);

    }else{
        return response()->json(null, 500);

    }
    }
    protected function getRequisitionsMyPersonalPendientes(){
        if(Auth::check()){
            $array=[];

   $signs= DmiBucketSignature::where('seg_seccion_id',12)
    ->where('personal_intelisis_usuario_ad',auth()->user()->usuario)
    ->where(function($query) {
        $query->where("status","pendiente");
    })->where("seg_seccion_id",12)->orderBy("origin_record_id","desc")->get();

    $res = DB::table("dmicontrol_signatures_behalves")->where("behalf_usuario_ad",auth()->user()->usuario)->where('seg_seccion_id',12)
    ->select('usuario_ad');
    $signs2= DmiBucketSignature::where('seg_seccion_id',12)
    ->whereIn('personal_intelisis_usuario_ad', $res)
    ->where(function($query) {
        $query->where("status","pendiente");
    })->where("seg_seccion_id",12)->orderBy("origin_record_id","desc")->get();

    if(count($signs)>0){
        foreach ($signs as $key => $value) {
            $res2=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])
            ->where("id",intval($value->origin_record_id))->where("status","recaudar firmas")->first();
           if(isset($res2)){
            array_push($array,$res2);
            end($array);
            $k = key($array);
            if($res2!= null){
                $array[$k]['sign_status'] = $value->status;
            }
            if($res2->file!= null){
                $file= Storage::disk("Requisitions")->url($res2->file);
                $array[$k]['url']=$file;
            }else{
                $array[$k]['url']=null;
            }
        }
        }
}
if(count($signs2)>0){
    $type= DB::table('dmicontrol_signatures_behalves')
                        ->join('dmicontrol_process', 'dmicontrol_signatures_behalves.dmi_control_process_id', '=', 'dmicontrol_process.id')
                        ->where('dmicontrol_signatures_behalves.behalf_usuario_ad',auth()->user()->usuario)
                        ->where('seg_seccion_id',12)
                        ->select('dmicontrol_process.name')->distinct()
                        ->get();

    foreach ($signs2 as $key => $value) {
        if(count($type)>1){
            $res3=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])
            ->where("id",intval($value->origin_record_id))
            ->where(function($query) use($type) {
                $query->where("type",$type[0]->name)
                ->orWhere("type",$type[1]->name);
            })->where("status","recaudar firmas")->first();
        }else{
            $res3=DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment'])
            ->where("id",intval($value->origin_record_id))->where("type",$type[0]->name)->where("status","recaudar firmas")->first();

        }

       if($res3!=null){
        array_push($array,$res3);
        end($array);
        $k = key($array);
        if($res3!= null){
            $array[$k]['sign_status'] = $value->status;
        }
        if($res3->file!= null){
            $file= Storage::disk("Requisitions")->url($res3->file);
            $array[$k]['url']=$file;
        }else{
            $array[$k]['url']=null;
        }
     }
    }
}

    return response()->json($array, 200);

}else{
    return response()->json(null, 500);

}
}

    public function rejectedSignature($_work_permit_id){
        $cancel_signatures = DmiBucketSignature::where('origin_record_id',$_work_permit_id)
                        ->where('seg_seccion_id',12)
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

        //$this->sendConfirmationEmail($permit->id);
    }
    public function generarPDFPersonalRequisition($data,$signatures,$audit){

        //$_id = 4;
        // $data = DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis'])->find($_id);

        $pdf = PDF::loadView('PDF.DMI.RRHH.personal_requisition',  [
            'data' =>  $data,
            'sign' =>  $signatures,
            'audit' => $audit

        ], [], [
            'format' => 'A4',
        ]);

		//return $pdf->stream('PDF.DMI.RRHH.work_permit');
		//return view("PDF.DMI.RRHH.work_permit")->with(compact("data"));

		if(!file_exists('storage/Requisitions')) {
			mkdir("storage/Requisitions", 0700);
		}

        $file_name = $data->id.'_'.time().'_personal_requisition.pdf';
        $path = "storage/Requisitions/{$file_name}";
        $pdf->save(public_path($path));

        if($pdf != null){
            $work_permit = DmirhPersonalRequisition::where('id',$data->id)->first();
            $work_permit->document = $file_name;
            $work_permit->save();
        }

    }

    public function apiGeneretePDFPersonalRequisition($_personal_requisition_id){

        if(Auth::check()){
            if(isset($_personal_requisition_id) || $_personal_requisition_id > 0){

                $data = DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis','dmi_personal_substitution'])->find($_personal_requisition_id);
                
                if($data != null){
                    $signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$_personal_requisition_id)->where('seg_seccion_id',12)->orderBy("order","ASC")->orderBy("id","ASC")->get();
                    $signAudit= DmiControlSignaturesBehalfAudit::with('personal_intelisis_requisition')->where("origin_record_id",$_personal_requisition_id)->get();// $data["signatures"]=$signatures;
                    $this->generarPDFPersonalRequisition($data, $signatures,$signAudit);
                }else{
                    return ["message"=>"No se encontro la requisición solicitada"];
                }
                
            }else{
                return ["message"=>"Id inválido"];
            }
        }else{
            return ["message"=>"Necesita iniciar sesion"];
        }
        
        

    }

    protected function GenerateRequisitionTemp(Request $request){
        $data = DmirhPersonalRequisition::with(['dmi_control_email_domain','dmi_cat_status_recruitment','personal_intelisis','personal_intelisis_plaza','dmi_personal_substitution'])->find($request->id);
        // $signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$request->id)->where("status","firmado")->where('seg_seccion_id',12)->get();
        // $data["signatures"]=$signatures;
        $signatures = DmiBucketSignature::with('personal_intelisis')->where('origin_record_id',$request->id)->where('seg_seccion_id',12)->orderBy("order","ASC")->orderBy("id","ASC")->get();
        $signAudit= DmiControlSignaturesBehalfAudit::with('personal_intelisis_requisition')->where("origin_record_id",$request->id)->get();// $data["signatures"]=$signatures;
        $pdf = PDF::loadView('PDF.DMI.RRHH.personal_requisition',  [
            'data' =>  $data,
            'sign' =>  $signatures,
            'audit' => $signAudit
        ], [], [
            'format' => 'A4',
        ]);



		if(!file_exists('storage/Requisitions/Temp')) {
			mkdir("storage/Requisitions/Temp", 0700);
		}else{
           $this->cleanDirectory('Temp');

        }

        $file_name = $data->id.'_'.time().'_personal_requisition.pdf';
        $path = "storage/Requisitions/Temp/{$file_name}";
        $pdf->save(public_path($path));

        return response()->json(["url"=> Storage::disk('public')->url("Requisitions/Temp/{$file_name}"),'prueba'=>$data ], 200);

    }

    function cleanDirectory($path, $recursive = false)
      {
    $storage = Storage::disk('Requisitions');

    foreach($storage->files($path, $recursive) as $file) {
        $storage->delete($file);
    }
}
    protected function updateStatusRecruitment(){
        $datos= $this->validate(request(),[

          'id' => 'required',
          'status_recruitment_id' => 'required',
          ]);

        if(Auth::check()){

          try {
              $req= DmirhPersonalRequisition::find($datos["id"]);

            $req->status_recruitment_id= $datos["status_recruitment_id"];
            $req->save();

            } catch (\Throwable $th) {
              return response()->json(['error'=>'Error al modificar.'],500);
            }


           return response()->json(['success'=>'Estatus de Reclutamiento modificado Correctamente.'],200);
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }

      }

    public function getCoordinadoraRRHHLocation(){
        $control_authorization_signatures = DmiControlAuthorizationSignature::join('dmicontrol_process','dmicontrol_authorization_signatures.dmi_control_process_id','=','dmicontrol_process.id')
																->where('dmicontrol_process.name','Coordinadora_RRHH')
																->where('dmicontrol_authorization_signatures.plaza_id',auth()->user()->personal_intelisis->plaza_id)
																->whereNull('dmicontrol_process.deleted_at')
																->whereNull('dmicontrol_authorization_signatures.deleted_at')
																->select('dmicontrol_process.*')
																->get()
																->pluck('location');

        return sizeof($control_authorization_signatures) > 0 ? $control_authorization_signatures : null;
    }

    public function userWhoCanValidate(){
        $user_validator= DmiControlProcedureValidation::where('key','Requisition_PanelRequisition_user_who_can_validate-usuario_ad')
                                                                ->where('value',auth()->user()->usuario)
                                                                ->first();
                                                                
        if($user_validator){
            return ["success" => 1];
        }else{
            return ["success" => 0];
        }
    }

}
