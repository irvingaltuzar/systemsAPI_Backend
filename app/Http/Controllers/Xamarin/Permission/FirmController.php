<?php

namespace App\Http\Controllers\Xamarin\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Intranet\PersonalIntelisis;
use App\Models\Intranet\DmiSolicitud;
use App\Models\Intranet\DmiFirma;
use App\Models\Intranet\requestPermission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailAddFirm;

class FirmController extends Controller
{
    public function updateFirm($llave,$usuario){
        $solicitud=DmiSolicitud::where('llave',$llave)->first();
        $permiso=requestPermission::where('firma',$llave)->first();
        $addFirma=DmiFirma::where('llave',$llave)->where('nombre',$usuario)->first();
        $personalIntelisis=PersonalIntelisis::where('idpersonal',$solicitud->referencia)->first();
        $tiempo=time(); 
        $addFirma->token=$tiempo;
        $addFirma->fecha=date("Y-m-d");
        $addFirma->hora=date("H:i:s");
        $addFirma->estatus='completada';
        $addFirma->update();
        $firmas=DmiFirma::where('llave',$llave)->orderBy('orden', 'asc')->get();
        $pdf = new Fpdf;
        $pdf->AddPage();
        $widthPDF = $pdf->GetPageWidth();
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(.3);

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(0, 5, 'SOLICITUD DE PERMISO', 0, 0, 'C');
        $pdf->Ln(8);
        
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 8, 'Datos Generales', 'LBTR', 0, 'C', true);
        $pdf->Ln();
        $width = ($widthPDF - 20) / 4;

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell($width, 7, utf8_decode("No. Empleado:"), 'LBR', 0, 'L', false);
        $pdf->Cell($width, 7, $personalIntelisis->idpersonal, 'BR', 0, 'C', false);
        $pdf->Cell($width, 7, "Fecha de solicitud:", 'BR', 0, 'C', false);
        $pdf->Cell($width, 7, Carbon::parse($solicitud->fecha)->format('d/m/Y'), 'BR', 0, 'C', false);
        $pdf->Ln();
       
        $pdf->Cell($width, 7, "Nombre: ", 'LRB', 0, 'L', false);
        $pdf->Cell(0, 7, utf8_decode($personalIntelisis->nombre.' '.$personalIntelisis->apellidos), 'RB', 0, 'C', false);
        $pdf->Ln();
        
        $pdf->Cell($width, 7, utf8_decode("Razón Social:"), 'LRB', 0, 'L', false);
        $pdf->Cell(0, 7, utf8_decode($personalIntelisis->nombreempresa), 'RB', 0, 'C', false);
        $pdf->Ln();
        
        $pdf->Cell($width, 7, utf8_decode("Ubicación:"), 'LRB', 0, 'L', false);
        $pdf->Cell($width, 7, utf8_decode($personalIntelisis->ubicacion), 'RB', 0, 'C', false);
        $pdf->Cell(30, 7, utf8_decode("Departamento:"), 'RB', 0, 'C', false);
        $pdf->Cell(0, 7, utf8_decode($personalIntelisis->departamento), 'RB', 0, 'C', false);
        $pdf->Ln();
        
        $pdf->Cell($width, 7, utf8_decode("Puesto:"), 'LBR', 0, 'L', false);
        $pdf->Cell($width + 30, 7, utf8_decode($personalIntelisis->puesto), 'BR', 0, 'L', false);
        $pdf->Cell($width - 8, 7, utf8_decode("Fecha Recepción RH:"), 'BR', 0, 'C', false);
        $pdf->Cell($width - 22, 7, "", 'BR', 0, 'C', false);
        $pdf->Ln(12);
        
        $pdf->SetFont('Arial', 'B');
        $pdf->Cell(0, 7, 'PERMISOS', 'LBTR', 0, 'C', true);
        $pdf->Ln();

        $pdf->SetFont('Arial', '');
        $pdf->Cell($width, 14, 'Fecha Solicitada:', 'LBR');
        $pdf->Cell(20, 7, 'Del', 'BR', 0, 'C');
        $pdf->SetFont('Arial', 'B'); 
        
        $date = Carbon::parse($permiso->fechainicio)->locale('es');
        $date= utf8_decode(ucfirst($date->isoFormat('dddd')).", ".$date->format('d')." de ".ucfirst($date->monthName).' de '.$date->format('Y'));
        $pdf->Cell(0, 7, $date, 'BR', 0, 'L');
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '');
        $pdf->Cell($width, 7, '');
        $pdf->Cell(20, 7, 'Al', 'BR', 0, 'C');
        $pdf->SetFont('Arial', 'B');
        $date1 = Carbon::parse($permiso->fechafin)->locale('es');
        $date1= utf8_decode(ucfirst($date1->isoFormat('dddd')).", ".$date1->format('d')." de ".ucfirst($date1->monthName).' de '.$date1->format('Y'));
        $pdf->Cell(0, 7, $date1, 'BR', 0, 'L');
        $pdf->Ln(12);
        
        $pdf->SetFont('Arial','');
        $pdf->Cell($width,10,'Con goce de sueldo','LBTR',0,'C');
        $pdf->SetFont('Arial','B');
        $pdf->Cell($width,10,($permiso->goce!='N')?'Si':'-','LBTR',0,'C',true);
        
        $pdf->SetFont('Arial','');
        $pdf->Cell($width,10,'Sin goce de sueldo','BTR',0,'C');
        $pdf->SetFont('Arial','B');
        $pdf->Cell($width,10,($permiso->goce=='N')?'Si':'-','LBTR',0,'C',true);
        $pdf->Ln(12);

        $pdf->SetFont('Arial','');
        $pdf->Cell(0,7,'Motivo del permiso:','LBTR',0,'C',true);
        $pdf->Ln();

        $pdf->SetFont('Arial','B');
        $pdf->MultiCell(0,6,utf8_decode($permiso->motivo),'LR');
        $pdf->SetFont('Arial','');
        
        $pdf->MultiCell(0,6,utf8_decode($permiso->observaciones),'LBR');
        $pdf->Cell(0,5,'',0,0,'C',false);
        $pdf->Ln();

        $pdf->SetFont('Arial','B');
        $pdf->Cell(20,7,'Nota:','LBT',0,'R',false);
        $pdf->SetFont('Arial','');
        $pdf->Cell(0,7,'Si es un permiso con goce de sueldo se requiere firma del Director de Unidad de Negocio','BTR',0,'L',false);
        $pdf->Ln();
        
        $pdf->SetFont('Arial','B');
        $pdf->Cell(0,7,'AUTORIZACIONES:','LBTR',0,'C',true);
        $pdf->Ln();        
        
        $empleado="";
        $firma_empleado="";
        $token_empleado="";
        $fecha_empleado="";

        $jefe_inmediato="";
        $firma_jefe="";
        $token_jefe="";
        $fecha_jefe="";

        $cordinador="";
        $firma_cor="";
        $token_cor="";
        $fecha_cor="";
        $puesto_cor="";

        $gerente="";
        $puesto_ger="";
        $firma_ger="";
        $token_ger="";
        $fecha_ger="";
      
        foreach ($firmas as $value) {
            
            if($value->orden==1){
                $empleado=$value->nombre;
                if($value->estatus=='completada'){
                    $firma_empleado=$this->firma($value->nombre);
                 
                    $token_empleado="Firma digital - ".$value->token;
                    $fecha_empleado=$value->fecha." ".$value->hora;
                }
            }else if($value->orden==2){
                $jefe_inmediato=$value->nombre;
                if ($value->estatus=='completada') {
                    $firma_jefe=$this->firma($value->nombre);
                    $token_jefe="Firma digital - ".$value->token;
                    $fecha_jefe=$value->fecha." ".$value->hora;
                }
            }else if($value->orden==3){
               
                $cordinador=$value->nombre;
                
                $persona=PersonalIntelisis::where(DB::raw("CONCAT(nombre, ' ', apellidos)"),$cordinador)->first();
                $puesto_cor=$persona->puesto;
                if ($value->estatus=='completada') {
                    $firma_cor=$this->firma($value->nombre);
                    $token_cor="Firma digital - ".$value->token;
                    $fecha_cor=$value->fecha." ".$value->hora;
                }
            }else if($value->orden==4){
                $gerente=$value->nombre;
                $persona=PersonalIntelisis::where(DB::raw("CONCAT(nombre, ' ', apellidos)"),$cordinador)->first();
                $puesto_ger=$persona->puesto;
                if ($value->estatus=='completada') {
                    $firma_ger=$this->firma($value->nombre);
                    $token_ger="Firma digital - ".$value->token;
                    $fecha_ger=$value->fecha." ".$value->hora;
                }
            }
            
        }
       
        $pdf->AddFont('FallisComing-Regular','B','fallis.php');
        $pdf->SetFont('FallisComing-Regular','B',22);
        $pdf->Cell($width*2,15,'','LR',0,'I');
        $pdf->Cell($width*2,15,'','LR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,6,"    ".utf8_decode($firma_empleado),'LR',0,'I');
        $pdf->Cell($width*2,6,"    ".utf8_decode($firma_jefe),'LR',0,'I');
        $pdf->SetFont('Arial','',11);
        $pdf->Cell($width*2,6,'','LR',0,'C');
        $pdf->Ln();
        $pdf->SetFont('Arial','I',7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell($width*2,2,"        ".$token_empleado,'LR',0,'I');
        $pdf->Cell($width*2,2,"        ".$token_jefe,'LR',0,'I');
        $pdf->Ln();
        $pdf->SetFont('Arial','I',7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell($width*2,2,"        ".$fecha_empleado,'LR',0,'I');
        $pdf->Cell($width*2,2,"        ".$fecha_jefe,'LR',0,'I');
        $pdf->Ln();
       
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell($width*2,5,utf8_decode($empleado),'LR',0,'C');
        $pdf->Cell($width*2,5,utf8_decode($jefe_inmediato),'LR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,5,'','LBR',0,'C');
        $pdf->Cell($width*2,5,'','LBR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,7,"Firma del Empleado",'LBR',0,'C');
        $pdf->Cell($width*2,7,"Jefe Inmediato",'LBR',0,'C');
        $pdf->Ln();
        
        $pdf->AddFont('FallisComing-Regular','B','fallis.php');
        $pdf->SetFont('FallisComing-Regular','B',22);
        $pdf->Cell($width*2,15,'','LR',0,'I');
        $pdf->Cell($width*2,15,'','LR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,6,"    ".utf8_decode($firma_cor),'LR',0,'I');
        $pdf->Cell($width*2,6,"    ".utf8_decode($firma_ger),'LR',0,'I');
        $pdf->SetFont('Arial','',11);
        $pdf->Cell($width*2,6,'','LR',0,'C');
        $pdf->Ln();
        $pdf->SetFont('Arial','I',7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell($width*2,2,"        ".$token_cor,'LR',0,'I');
        $pdf->Cell($width*2,2,"        ".$token_ger,'LR',0,'I');
        $pdf->Ln();
        $pdf->SetFont('Arial','I',7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell($width*2,2,"        ".$fecha_cor,'LR',0,'I');
        $pdf->Cell($width*2,2,"        ".$fecha_ger,'LR',0,'I');
        $pdf->Ln();

        $pdf->SetFont('Arial','',11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell($width*2,5,utf8_decode($cordinador),'LR',0,'C');
        
        $pdf->Cell($width*2,5,utf8_decode($gerente),'LR',0,'C');
        $pdf->Ln();
        
        $pdf->Cell($width*2,5,'','LRB',0,'I');
        
        $pdf->Cell($width*2,5,'','LRB',0,'I');
        $pdf->Ln();
        $pdf->Cell($width*2,7,utf8_decode($this->firma($puesto_cor)),'LBR',0,'C');
        
        $pdf->Cell($width*2,7,utf8_decode($this->firma($puesto_ger)),'LBR',0,'C');
        $pdf->Ln();
        $nombre_pdf=$addFirma->usuario."_".$llave."_".$tiempo.".pdf";
        $nombre_arch='../../../../inetpub/wwwroot/intranet/FPDI/archivos_pdf/'.$nombre_pdf;
        if($pdf->Output($nombre_arch,"F")!=""){
            return ['status'=>'error','message'=>'Error al generar pdf'];
        }
        $addFirma->pdf=$nombre_pdf;
        if($addFirma->update()){
            $enviado=DmiFirma::where('llave',$llave)->where('orden',$addFirma->orden+1)->first();
            if($enviado){
                $enviado->estatus='enviado';
                $enviado->update();
                if($enviado->app=="permisos"){
                    $subject="Solicitud de permiso por autorizar";
                }else if($enviado->app=="vacaciones"){
                    $subject="Solicitud de Vacaciones por autorizar";
                }
                $usuario=DmiFirma::where('llave',$llave)->where('orden','1')->first();
                $data=[
                    "name"=>ucwords(strtolower($enviado->nombre)),
                    "content"=>"Te informamos que existe una solicitud pendiente a tu autorización con fecha inicio <strong>".
                    Carbon::parse($permiso->fechainicio)->format('d/m/Y')."</strong> y fecha fin <strong>".
                    Carbon::parse($permiso->fechafin)->format('d/m/Y').
                    "</strong> para <strong>".ucwords(strtolower($usuario->nombre))."</strong>",
                    "subject"=>$subject
                ];
               
                Mail::to($enviado->correo)
                ->send(new EmailAddFirm($data));
            }else{
                $solicitud->estatus='completada';
                $solicitud->update();
                $permiso->estatus='firma completada';
                $permiso->update();
                $usuario=DmiFirma::where('llave',$llave)->where('orden','1')->first();
                if($usuario->app=="permisos"){
                    $subject="Solicitud de permiso completada";
                }else if($usuario->app=="vacaciones"){
                    $subject="Solicitud de Vacaciones completada";
                }
                $data=[
                    "name"=>ucwords(strtolower($usuario->nombre)),
                    "content"=>"Te informamos que tu solicitud con fecha inicio <strong>".
                    Carbon::parse($permiso->fechainicio)->format('d/m/Y')."</strong> y fecha fin <strong>".
                    Carbon::parse($permiso->fechafin)->format('d/m/Y').
                    "</strong> fue completada",
                    "subject"=>$subject
                ];
               
                Mail::to($usuario->correo)
                ->send(new EmailAddFirm($data));
            }
            
            return ['status'=>'succes', 'message'=>'Se firmo correctamente'];
        }
        return ['status'=>'error','message'=>'Error al firmar'];
    }
    protected function firma($firma){
        $firma_digital = strtolower($firma);
        $firma_digital = ucwords($firma_digital);
        return $firma_digital;
    }
    public function addFirm(Request $request){
        $user = new LoginController();
        $usuario = $user->check();
        $firma=DmiFirma::where('llave',$request->firma)->first();

        if($firma->app=='permisos'){
            $nombre_usuario=$usuario->original[0]->nombre." ".$usuario->original[0]->apePat." ".$usuario->original[0]->apeMat;
            $add_firma=$this->updateFirm($request->firma,$nombre_usuario);
            if($add_firma["status"]!="succes"){
                return response()->json(['error' => $add_firma['message']], 500);
            }
            return response()->json(['success' => $add_firma['message']], 200);
        }else{
            
        }
        
    }
}
