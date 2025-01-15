<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ToolsController;
use App\Models\PersonalIntelisis;
use App\Models\AuditAutomatedProcess;
use App\Models\PayrollPdfNotGenerated;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Exception;
use App\Repositories\IntelisisRepository;
use App\Services\SendEmailService;
use App\Models\DmiControlProcedureValidation;

class WhatsAppController extends Controller
{

    private $ToolsController,$WhatsAppService,$IntelisisRepository,$SendEmailService;
    public $process_name="Envio de nómina automático";
    public $process_key;


    public function __construct(ToolsController $_ToolsController, WhatsAppService $_WhatsAppService, IntelisisRepository $_IntelisisRepository,
                                SendEmailService $_SendEmailService){
        $this->ToolsController = $_ToolsController;
        $this->WhatsAppService = $_WhatsAppService;
        $this->IntelisisRepository = $_IntelisisRepository;
        $this->SendEmailService = $_SendEmailService;
        $this->process_key = Carbon::now()->format("Ymd_his");

    }

    public function runJobSendPayrollWhatsApp(){
        $audit_process = new AuditAutomatedProcess();
        $audit_process->process_key = $this->process_key;
        $audit_process->process_name = $this->process_name;
        $audit_process->event = "Inicio Job (".__FUNCTION__.")";
        $audit_process->comments = "Inicio del proceso";
        $audit_process->save();

        $now = Carbon::now();
        //$now = Carbon::create(2024,5,15);

        // Se valida si es viernes para los semanales, pero se corre también en sábado y lunes
        if($now->dayOfWeek == 1 ||$now->dayOfWeek == 5 || $now->dayOfWeek == 6){
            // Se ejecuta el proceso - viernes
            //return "Se ejecuta el proceso - viernes";
            $result = $this->sendPayrollReceipts();
        }else{

            //Se hacen validaciones para los quincenales
            if($now->day == 15 || $now->day == 16){
                // Se ejecuta el proceso
                //return "Se ejecuta el proceso - ".$now->day;
                $result = $this->sendPayrollReceipts();
            }else{

                // Se valida febrero
                if($now->month == 2){
                    if($now->day == 28 || $now->day == 29){
                        // Se ejecuta el proceso
                        //return "Se ejecuta el proceso - ".$now->day;
                        $result = $this->sendPayrollReceipts();
                    }
                }else{
                    if($now->day == 30 || $now->day == 31 || $now->day == 1 || $now->day == 2){
                        // Se ejecuta el proceso
                        $result = $this->sendPayrollReceipts();
                    }else{
                        //return "No se ejecuta el proceso";
                        //Log::info('No se ejecuta el proceso');
                        
                    }
                }
            }
        }
    }

    // Funcional, pero obteniendo a los colaboradores que quieres que se les envie su nómina
    public function sendPayrollReceipts(){

        set_time_limit(60);

        $this->sendPreviouslyUnsentPayroll();
        $year = Carbon::now()->year;

         //AuditAutomatedProcess
         $audit_process = new AuditAutomatedProcess();
         $audit_process->process_key = $this->process_key;
         $audit_process->process_name = $this->process_name;
         $audit_process->event = "Inicio - Envio de nóminas (".__FUNCTION__.")";
         $audit_process->comments = "Inicio del proceso";
         $audit_process->save();

        // Se obtienen los colaboradores que quieren recibir la nómina
        $users_recieve_payroll = PersonalIntelisis::where('receive_payroll',1)
                                                    ->whereNotNull('phone_number')
                                                    ->where('status','ALTA')->get();
        $total_payroll_receipts_day = sizeof($users_recieve_payroll);

        if(sizeof($users_recieve_payroll) > 0){
            
            try {

                $count_payroll_sent=0;
                $count_payroll_not_send=0;
                $count_payroll_already_sent=0;

                //Se calcula el tiempo para que dure la petición y no termine a los 30 segundos
                $total_seconds = $total_payroll_receipts_day * 5;

                $total_seconds < 30 ? set_time_limit(30) : set_time_limit($total_seconds);
                
                foreach($users_recieve_payroll as $user){
                    // Se obtienen los PDF que se tienen que enviar
                    $user_payrolls = $this->ToolsController->getPayroll($year,$user->rfc)->getData(false);
                    
                    if(sizeof($user_payrolls) > 0){
                        $end_payroll = end($user_payrolls);

                        // Se valida si ya fue enviada esta nómina
                        $payroll_sent = AuditAutomatedProcess::where('process_name','Envio de nómina automático')
                                                                ->where("affected",$end_payroll->RID)
                                                                ->where('comments','like','%NOMINA ENVIADA%')
                                                                ->first();
                        
                        if($payroll_sent == null){
                            /* *************************************************************************************************** */
                            // Se obtiene el PDF
                            $payroll_pdf = $this->getPayrollPDF($end_payroll);
    
                            if($payroll_pdf['success'] == 1){
                                // Se envia primero un message template para iniciar la conversación
                                try {
                                    $start_conversation = $this->WhatsAppService->sendWhatsAppStartConversation([
                                        "collaborador_name" => $user->name,
                                        "recipient_phone_number" => $user->phone_number,
                                    ]);
                                } catch (\Exception $exc) {
                                    $audit_process = new AuditAutomatedProcess();
                                    $audit_process->process_key = $this->process_key;
                                    $audit_process->process_name = $this->process_name;
                                    $audit_process->affected = $end_payroll->RID;
                                    $audit_process->event = "Error enviar messageTemplate (".__FUNCTION__.")";
                                    $audit_process->error = "Error -> #message: ".$exc->getMessage().' -> #file: '.$exc->getFile().' -> #line: '.$exc->getLine();
                                    $audit_process->save();
                                }

                                // Se envia por Whats el recibo
                                $payroll_data = [
                                    "title" => str_replace('Nomina','nómina',$end_payroll->Observaciones),
                                    "document_name" => "recibo_".$end_payroll->Ejercicio.".pdf",
                                    "document" => $payroll_pdf['data'],
                                    "phone_number" => $user->phone_number,
                                    "document_content_type" => "application/pdf",
                                ];
                            
                                $payroll_whats = $this->WhatsAppService->sendTemplatePayrollWhatsAppCloud($payroll_data);

                                if($payroll_whats['success'] == 1){

                                    $audit_process = new AuditAutomatedProcess();
                                    $audit_process->process_key = $this->process_key;
                                    $audit_process->process_name = $this->process_name;
                                    $audit_process->affected = $end_payroll->RID;
                                    $audit_process->event = "Se envió correctamente el recibo por WhatsApp (".__FUNCTION__.")";
                                    $audit_process->comments = json_encode([
                                                                    "envio" => "success",
                                                                    "rfc" => $user->rfc,
                                                                    "celular" => $user->phone_number,
                                                                    "wa.id" => $payroll_whats['data']['message_id'],
                                                                    "comment" => "NOMINA ENVIADA",
                                                                ]);
                                    $audit_process->save();
                                    
                                    $count_payroll_sent++;

                                }else{

                                    $audit_process = new AuditAutomatedProcess();
                                    $audit_process->process_key = $this->process_key;
                                    $audit_process->process_name = $this->process_name;
                                    $audit_process->affected = $end_payroll->RID;
                                    $audit_process->event = "Error al enviar el recibo por WhatsApp (".__FUNCTION__.")";
                                    $audit_process->comments = json_encode([
                                                                    "message" => $payroll_whats['message'],
                                                                    "error" => $payroll_whats['error'],
                                                                ]);
                                    $audit_process->save();

                                    $count_payroll_not_send++;
                                }


                            }else{
                                $audit_process = new AuditAutomatedProcess();
                                $audit_process->process_key = $this->process_key;
                                $audit_process->process_name = $this->process_name;
                                $audit_process->affected = $end_payroll->RID;
                                $audit_process->event = "Obtener la nómina PDF (".__FUNCTION__.")";
                                $audit_process->comments = "No se pudo obtener el PDF de la nómina - RFC: ".$end_payroll->RFC." - Observaciones: ".$end_payroll->Observaciones;
                                $audit_process->save();

                                $count_payroll_not_send++;

                            }
                            /* **************************************************************************************************** */

                        }else{

                            $audit_process = new AuditAutomatedProcess();
                            $audit_process->process_key = $this->process_key;
                            $audit_process->process_name = $this->process_name;
                            $audit_process->affected = $end_payroll->RID;
                            $audit_process->event = " Validación de nómina enviada (".__FUNCTION__.")";
                            $audit_process->comments = "La nómina ya ha sido enviada anteriormente por WhatsApp - RFC: ".$end_payroll->RFC." - Observaciones: ".$end_payroll->Observaciones;
                            $audit_process->save();

                            $count_payroll_already_sent++;
                        }


                    }else{
                        //AuditAutomatedProcess
                        $audit_process = new AuditAutomatedProcess();
                        $audit_process->process_key = $this->process_key;
                        $audit_process->process_name = $this->process_name;
                        $audit_process->event = "Obtener nómina del colaborador (".__FUNCTION__.")";
                        $audit_process->affected = $end_payroll->RID;
                        $audit_process->comments = "No se encontró ningún nómina del colaborador del ERP ".$end_payroll->RFC;
                        $audit_process->save();
                        
                    }
                    

                }
            
            } catch (\Exception $exc) {
                //AuditAutomatedProcess
                $audit_process = new AuditAutomatedProcess();
                $audit_process->process_key = $this->process_key;
                $audit_process->process_name = $this->process_name;
                $audit_process->event = "Error generado (".__FUNCTION__.")";
                $audit_process->error = "Error -> #message: ".$exc->getMessage().' -> #file: '.$exc->getFile().' -> #line: '.$exc->getLine();
                $audit_process->save();
            }
            

            //AuditAutomatedProcess
            $audit_process = new AuditAutomatedProcess();
            $audit_process->process_key = $this->process_key;
            $audit_process->process_name = $this->process_name;
            $audit_process->event = "Fin - Envio de nóminas (".__FUNCTION__.")";
            $audit_process->comments = "Fin del proceso con éxito -> [ Enviados: $count_payroll_sent, Enviados anteriormente: $count_payroll_already_sent, No enviados: $count_payroll_not_send ] = Total de Usuarios a Enviar: $total_payroll_receipts_day";
            $audit_process->save();

            $this->deletePayrollAfterSeveralAttempts();
            $this->sendEmailUnsentPayroll();

            return ['success' => 1, "data" =>$audit_process ,"message" => ""];

        }else{
            //AuditAutomatedProcess
            $audit_process = new AuditAutomatedProcess();
            $audit_process->process_key = $this->process_key;
            $audit_process->process_name = $this->process_name;
            $audit_process->event = "Fin - Envio de nóminas (".__FUNCTION__.")";
            $audit_process->comments = "No se encontró ningún colaborador que recibe nómina";
            $audit_process->save();
            
            return ['success' => 0 , "data" =>$audit_process ,"message" => "No se obtuvieron registros de ERP"];
        }
        

    }

    public function sendPreviouslyUnsentPayroll(){
        //AuditAutomatedProcess
        $audit_process = new AuditAutomatedProcess();
        $audit_process->process_key = $this->process_key;
        $audit_process->process_name = $this->process_name;
        $audit_process->event = "Inicio - Envio de nóminas no enviadas anteriormente (".__FUNCTION__.")";
        $audit_process->comments = "Inicio del proceso";
        $audit_process->save();

        $unsents_payroll = PayrollPdfNotGenerated::all();
        $total_payroll_receipts_day = sizeof($unsents_payroll);

        if(sizeof($unsents_payroll) > 0){

            try{
                $count_payroll_sent=0;
                $count_payroll_not_send=0;
                $count_payroll_already_sent=0;

                //Se calcula el tiempo para que dure la petición y no termine a los 30 segundos
                $total_seconds = $total_payroll_receipts_day * 5;
                $total_seconds < 30 ? set_time_limit(30) : set_time_limit($total_seconds);

                foreach($unsents_payroll as $payroll){

                    $data_payroll_erp = json_decode($payroll->data_payroll);

                    /* ****************************************************************************************************** */
                    // Se valida si ya fue enviada esta nómina
                        $payroll_sent = AuditAutomatedProcess::where('process_name','Envio de nómina automático')
                                                                ->where("affected",$payroll->rid)
                                                                ->where('comments','like','%NOMINA ENVIADA%')
                                                                ->first();
        
                        if($payroll_sent == null ){
                            // Se obtiene al colaborador para obtener el su número de celular
                            $personal_intelisis = PersonalIntelisis::where("rfc",$payroll->rfc)
                                                                    ->where('status',"ALTA")
                                                                    ->first();
                            if($personal_intelisis != null){
        
                                // Si tiene número de celular, entonce si podemos enviarle el recibo
                                if($personal_intelisis->phone_number != null){
                                    // Se obtiene el PDF
                                    $payroll_pdf = $this->getPayrollPDF($data_payroll_erp);
        
                                    if($payroll_pdf['success'] == 1){
                                        // Se envia primero un message template para iniciar la conversación
                                        try {
                                            $start_conversation = $this->WhatsAppService->sendWhatsAppStartConversation([
                                                "collaborador_name" => $personal_intelisis->name,
                                                "recipient_phone_number" => $personal_intelisis->phone_number,
                                            ]);
                                        } catch (\Exception $exc) {
                                        }
        
                                        // Se envia por Whats el recibo
                                        $payroll_data = [
                                            "title" => str_replace('Nomina','nómina',$data_payroll_erp->Observaciones),
                                            "document_name" => "recibo_".$data_payroll_erp->Ejercicio.".pdf",
                                            "document" => $payroll_pdf['data'],
                                            "phone_number" => $personal_intelisis->phone_number,
                                            "document_content_type" => "application/pdf",
                                        ];
                                    
                                        $payroll_whats = $this->WhatsAppService->sendTemplatePayrollWhatsAppCloud($payroll_data);

        
                                        if($payroll_whats['success'] == 1){
        
                                            $audit_process = new AuditAutomatedProcess();
                                            $audit_process->process_key = $this->process_key;
                                            $audit_process->process_name = $this->process_name;
                                            $audit_process->affected = $data_payroll_erp->RID;
                                            $audit_process->event = "Se envió correctamente el recibo por WhatsApp (".__FUNCTION__.")";
                                            $audit_process->comments = json_encode([
                                                                    "envio" => "success",
                                                                    "rfc" => $personal_intelisis->rfc,
                                                                    "celular" => $personal_intelisis->phone_number,
                                                                    "wa.id" => $payroll_whats['data']['message_id'],
                                                                    "comment" => "NOMINA ENVIADA",
                                                                ]);
                                            $audit_process->save();

                                            $payroll->delete();
                                            
                                            $count_payroll_sent++;

                                        }else{
        
                                            $audit_process = new AuditAutomatedProcess();
                                            $audit_process->process_key = $this->process_key;
                                            $audit_process->process_name = $this->process_name;
                                            $audit_process->affected = $payroll->RID;
                                            $audit_process->event = "Error al enviar el recibo por WhatsApp (".__FUNCTION__.")";
                                            $audit_process->comments = json_encode([
                                                                            "message" => $payroll_whats['message'],
                                                                            "error" => $payroll_whats['error'],
                                                                        ]);
                                            $audit_process->save();

                                            $payroll->attempts_send = intval($payroll->attempts_send) + 1;

                                            $count_payroll_error++;
                                        }
        
        
                                    }else{
                                        $audit_process = new AuditAutomatedProcess();
                                        $audit_process->process_key = $this->process_key;
                                        $audit_process->process_name = $this->process_name;
                                        $audit_process->affected = $data_payroll_erp->RID;
                                        $audit_process->event = "Obtener la nómina PDF (".__FUNCTION__.")";
                                        $audit_process->comments = "No se pudo obtener el PDF de la nómina - RFC: ".$data_payroll_erp->RFC." - Observaciones: ".$data_payroll_erp->Observaciones;
                                        $audit_process->save();

                                        $payroll->attempts_send = intval($payroll->attempts_send) + 1;
                                        $payroll->save();

                                        $this->count_payroll_not_send++;
                                        
                                    }
                                    
                                }else{
                                    $audit_process = new AuditAutomatedProcess();
                                    $audit_process->process_key = $this->process_key;
                                    $audit_process->process_name = $this->process_name;
                                    $audit_process->affected = $payroll->nomina_personal;
                                    $audit_process->event = "Validación de número de celular (".__FUNCTION__.")";
                                    $audit_process->comments = "El colaborador no cuenta con número de celular.";
                                    $audit_process->save();

                                    $payroll->attempts_send = intval($payroll->attempts_send) + 1;
                                }   
        
                            }else{
        
                                $audit_process = new AuditAutomatedProcess();
                                $audit_process->process_key = $this->process_key;
                                $audit_process->process_name = $this->process_name;
                                $audit_process->affected = $payroll->Personal;
                                $audit_process->event = "Obtener al Personal Intelisis (".__FUNCTION__.")";
                                $audit_process->comments = "No se encontró al colaborador: ".$payroll->Personal;
                                $audit_process->save();

                                $payroll->attempts_send = intval($payroll->attempts_send) + 1;
        
                            }
        
                        }else{
                            $audit_process = new AuditAutomatedProcess();
                            $audit_process->process_key = $this->process_key;
                            $audit_process->process_name = $this->process_name;
                            $audit_process->affected = $payroll->RID;
                            $audit_process->event = " Validación de nómina enviada (".__FUNCTION__.")";
                            $audit_process->comments = "La nómina ya ha sido enviada anteriormente por WhatsApp - RFC: ".$payroll->RFC." - Observaciones: ".$payroll->Observaciones;
                            $audit_process->save();

                            $payroll->attempts_send = intval($payroll->attempts_send) + 1;

                            $count_payroll_already_sent++;
                        }
                        
                    /* ****************************************************************************************************** */

                }
            } catch (\Exception $exc) {
                //AuditAutomatedProcess
                $audit_process = new AuditAutomatedProcess();
                $audit_process->process_key = $this->process_key;
                $audit_process->process_name = $this->process_name;
                $audit_process->event = "Error generado (".__FUNCTION__.")";
                $audit_process->error = "Error -> #message: ".$exc->getMessage().' -> #file: '.$exc->getFile().' -> #line: '.$exc->getLine();
                $audit_process->save();
            }
            

            //AuditAutomatedProcess
            $audit_process = new AuditAutomatedProcess();
            $audit_process->process_key = $this->process_key;
            $audit_process->process_name = $this->process_name;
            $audit_process->event = "Fin - Envio de nóminas no enviadas anteriormente (".__FUNCTION__.")";
            $audit_process->comments = "Fin del proceso con éxito -> [ Enviados: $count_payroll_sent, Enviados anteriormente: $count_payroll_already_sent, No enviados: $count_payroll_not_send ] = Total de Usuarios a Enviar: $total_payroll_receipts_day";
            $audit_process->save();

            return ['success' => 1, "data" =>$audit_process ,"message" => ""];

        }else{
            //AuditAutomatedProcess
            $audit_process = new AuditAutomatedProcess();
            $audit_process->process_key = $this->process_key;
            $audit_process->process_name = $this->process_name;
            $audit_process->event = "Fin - Envio de nóminas no enviadas anteriormente (".__FUNCTION__.")";
            $audit_process->comments = "No se encontró ningúna registro";
            $audit_process->save();
            
            return ['success' => 0 , "data" =>$audit_process ,"message" => "No se encontró ningúna registro"];
        }

    }

    public function getPayrollPDF($_payroll, $_process = null){
        
        try {

            $temp_request = new Request();
            $temp_request->setMethod('POST');
            $temp_request->query->add(['payroll'=>['company_code' => $_payroll->Empresa,
                                                    'year' => $_payroll->Ejercicio,
                                                    'start_month' => $_payroll->Periodo,
                                                    'payroll_code' => $_payroll->MovID,
                                                    'rfc' => $_payroll->RFC,
                                                    ],
                                        'type' => 1
                                    ]);
            $document_pdf = $this->ToolsController->getFilePayroll($temp_request);
            
            return ['success' => 1, 'data' => base64_decode($document_pdf)];

        } catch (\Exception $exc) {
            //AuditAutomatedProcess
            $audit_process = new AuditAutomatedProcess();
            $audit_process->process_key = $this->process_key;
            $audit_process->process_name = $this->process_name;
            $audit_process->affected = $_payroll->RID;
            $audit_process->event = "No se obtuvo el PDF (".__FUNCTION__.") - RFC: ".$_payroll->RFC." - Observaciones: ".$_payroll->Observaciones;
            $audit_process->error = "Error -> #message: ".$exc->getMessage().' -> #file: '.$exc->getFile().' -> #line: '.$exc->getLine();
            $audit_process->save();


            $exists = PayrollPdfNotGenerated::where('rid',$_payroll->RID)->first();
            
            if($exists != null){
                $exists->attempts_send = intval($exists->attempts_send) + 1;
                $exists->save(); 
            }else{
                $not_generated = new PayrollPdfNotGenerated();
                $not_generated->rid = $_payroll->RID;
                $not_generated->data_payroll = json_encode($_payroll);
                $not_generated->rfc = $_payroll->RFC;
                $not_generated->attempts_send = 1;
                $not_generated->process_key = $this->process_key;
                $not_generated->save();
            }

            return ['success' => 0, 'message' => "No se pudo obtener el PDF de la nómina"];
       
        }
        
    }

    public function deletePayrollAfterSeveralAttempts(){

        $unsents_payroll = PayrollPdfNotGenerated::where('attempts_send','>=',3)->get();

        //AuditAutomatedProcess
        $audit_process = new AuditAutomatedProcess();
        $audit_process->process_key = $this->process_key;
        $audit_process->process_name = $this->process_name;
        $audit_process->event = "Eliminar nóminas que no se pudieron enviar más de 3 veces (".__FUNCTION__.")";
        $audit_process->comments = json_encode($unsents_payroll);
        $audit_process->save();

        $delete = PayrollPdfNotGenerated::where('attempts_send','>=',3)->delete();

    }

    public function sendEmailUnsentPayroll(){

        $unsents_payroll = PayrollPdfNotGenerated::with('personal_intelisis')->where('attempts_send','>=',1)->get();
        
		$payrolls_not_sent=[];
		

        try{
            foreach($unsents_payroll as $payroll){
                $payrolls_not_sent[]=(object) array(
                                        "personal_number" => json_decode($payroll->data_payroll)->Personal,
                                        "user_name" => $payroll->personal_intelisis != null ? $payroll->personal_intelisis->full_name : "Histórico",
                                        "payroll" => json_decode($payroll->data_payroll)->Observaciones,
                                        "location" => $payroll->personal_intelisis != null ? $payroll->personal_intelisis->location : "Histórico",
                                        "process_key" => $payroll->process_key,
                                    );
            }
            
            $user_to_notification = DmiControlProcedureValidation::join('personal_intelisis','personal_intelisis.usuario_ad','=','dmicontrol_procedure_validation.value')
                                                                    ->where('personal_intelisis.status','alta')
                                                                    ->whereNull('dmicontrol_procedure_validation.deleted_at')
                                                                    ->where('key','whatsApp_user_notification_unsent_payroll-usuario_ad')
                                                                    ->select('personal_intelisis.email')
                                                                    ->get()
                                                                    ->pluck('email');
            
            $data = [
                "to_email" => $user_to_notification,
                "data" => $payrolls_not_sent,
                "module" => "whatsapp_notification"
            ];
    
            $this->SendEmailService->notificationUnsentPayroll($data);

            $audit_process = new AuditAutomatedProcess();
            $audit_process->process_key = $this->process_key;
            $audit_process->process_name = $this->process_name;
            $audit_process->affected = null;
            $audit_process->event = "Notificación de PDF no generados de nómina.";
            $audit_process->comments = "Se envio correctamente el correo.";
            $audit_process->save();

        }catch(\Exception $exc){
            $audit_process = new AuditAutomatedProcess();
            $audit_process->process_key = $this->process_key;
            $audit_process->process_name = $this->process_name;
            $audit_process->affected = null;
            $audit_process->event = "Notificación de PDF no generados de nómina.";
            $audit_process->error = "Error -> #message: ".$exc->getMessage().' -> #file: '.$exc->getFile().' -> #line: '.$exc->getLine();
            $audit_process->save();
        }

		

    }

}