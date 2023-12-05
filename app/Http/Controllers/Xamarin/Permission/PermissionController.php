<?php

namespace App\Http\Controllers\Xamarin\Permission;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\Intranet\requestPermission;
use App\Models\Intranet\PersonalIntelisis;
use App\Models\Intranet\DmiSolicitud;
use App\Models\Intranet\DmiFirma;
use App\Models\Intranet\PermissionConcept;
use App\Models\Intranet\AnotherConceptPermission;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Xamarin\Permission\FirmController;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailAddFirm;

class PermissionController extends Controller
{
    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';
    protected function getPermission()
    {
        $user = Auth::user();
        $url=ENV('URL_FILES')."FPDI/archivos_pdf/";
        $res=DB::SELECT("select p.fechasolicitud,p.goce as type, p.estatus, p.fechainicio as start_date, p.fechafin as end_date, p.firma, 
        max(f.orden) as num, max(f.estatus) as estatus_firma, max(f.fecha) as fecha_firma, max(f.hora) as hora_firma, p.idsolicitud,
        (
                    select nombre from intranet.dmi_firmas 
                    where llave = s.llave and usuario = s.usuario and app = s.app and 
                    orden = (select max(orden) from intranet.dmi_firmas 
                         where llave = s.llave and usuario = s.usuario and app = s.app 
                         and (estatus = 'rechazada' or estatus = 'completada')
                         group by llave) LIMIT 1
                ) as firmo,
                concat('".$url."',(
                    select max(pdf) from intranet.dmi_firmas 
                    where llave = s.llave and usuario = s.usuario and app = s.app 
                    and (estatus = 'rechazada' or estatus = 'completada')
                    group by llave  LIMIT 1
        ) )as archivo, p.usuario
        from intranet.solicitudes_permisos as p
        inner join intranet.dmi_solicitudes as s on
        p.firma = s.llave and p.usuario = s.usuario
        left join intranet.dmi_firmas as f on
        s.llave = f.llave and s.usuario = f.usuario and s.app = f.app 
        and (f.estatus = 'rechazada' or f.estatus = 'completada')
        where s.app = 'permisos' and p.usuario = '".$user->usuario."'
        group by p.firma order by p.fechasolicitud desc
        ");

                        
        return response()->json($res, 200);
    }

    protected function store(Request $request)
    {
        try {
            $user = new LoginController();
            $usuario = $user->checkXamarin();
            $tiempo=time();
            $nombre_usuario=$usuario->original[0]->nombre." ".$usuario->original[0]->apePat." ".$usuario->original[0]->apeMat;
            $permission = new requestPermission();
            $permission->idpersonal = $usuario->original[0]->noempleado;
            $permission->fechainicio = Carbon::parse(str_replace("/", "-", $request->start_date))->locale('es');
            $permission->fechafin = Carbon::parse(str_replace("/", "-", $request->end_date))->locale('es');
            $permission->fecharegreso = Carbon::parse(str_replace("/", "-", $request->start_date))->locale('es');
            $permission->totaldias = $request->days_leave;
            $permission->fechasolicitud = Carbon::now()->format('Y-m-d H:i:s');
            $permission->estatus = 'solicitada';
            $permission->observaciones = $request->observations;
            $permission->goce = $request->type;
            $permission->motivo = $request->reason;
            $permission->documento_recuperado = "permisos_new/pdfs/".$usuario->original[0]->usuario."_".$tiempo.".pdf";
            $permission->firma = $tiempo;
            $permission->intelisis = 'no';
            $permission->usuario = $usuario->original[0]->usuario;

            $info_rh = DB::select($this->queryPuesto($request->type, $request->days_leave, $usuario->original[0]->ruta));
            $rh_correo = '';
            $rh_nombre = '';
            $rh_puesto = '';
            foreach ($info_rh as $value) {
                $rh_correo = $value->emailempresa;
                $rh_puesto = $value->puesto;
                $rh_nombre= $value->nombre.' '.$value->apellidos;
            }
            if($rh_nombre==""){
                $info_rh = DB::select($this->temporaryPosition($request->type, $request->days_leave, $usuario->original[0]->ruta));
                foreach ($info_rh as $value) {
                    $rh_correo = $value->emailempresa;
                    $rh_puesto = $value->puesto;
                    $rh_nombre= $value->nombre.' '.$value->apellidos;
                }
            }
            $archivo=$this->addPDF($request,$rh_nombre,$rh_puesto,$rh_correo,$tiempo);
            if($archivo["status"]!="succes"){
                return response()->json(['error' => $archivo['message']], 500);
            }
            if ($permission->save()) {
                
                $firma = new FirmController();
               
                $add_firma=$firma->updateFirm($tiempo,$nombre_usuario);
                
                if($add_firma["status"]!="succes"){
                    return response()->json(['error' => $add_firma['message']], 500);
                }
                return response()->json(['success' => 'Se guardo exitosamente.'], 200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    protected function cancel(Request $request){
        try {
            $idsolicitud=$request->idsolicitud;
            $solicitud=requestPermission::where('idsolicitud',$idsolicitud)->first();
            $solicitud->estatus="cancelada";
            if ($solicitud->update()) {
                return response()->json(['success' => 'Se canceló la solicitud exitosamente.'], 200);
            }
            return response()->json(['error' => 'Error al cancelar la solicitud.'], 500);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }
    protected function queryPuesto($goce, $dias, $ruta)
    {

        $query = "select p.idpersonal, p.nombre, p.apellidos, p.puesto, p.emailempresa
			from intranet.personal_intelisis as p
			inner join intranet.usuario as u
			on u.nombre = p.nombre and p.apellidos = u.apellidos
            where";
        $ruta = explode("/", $ruta);
        $ruta = $ruta[1];
        if ($goce == "O") {
            $query .= " p.puesto like '%GERENTE%' AND p.departamento = 'RECURSOS HUMANOS' and estatus = 'ALTA' limit 1";
        } else if ($goce == "S") {
            $query .= " p.puesto like '%COORDINADOR%' AND p.departamento = 'RECURSOS HUMANOS' and  u.ruta like '%$ruta%'
            and estatus = 'ALTA' limit 1";
        } else if ($goce == "N" && $dias < 4) {
            $query .= " p.puesto like '%COORDINADOR%' AND p.departamento = 'RECURSOS HUMANOS' and  u.ruta like '%$ruta%'
            and estatus = 'ALTA' limit 1";
        } else if ($goce == "N" && $dias > 3) {
            $query .= " p.puesto like '%GERENTE%' AND p.departamento = 'RECURSOS HUMANOS' and estatus = 'ALTA' limit 1";
        }
        return $query;
    }
    protected function addPDF($request,$rh_nombre, $rh_puesto,$rh_correo,$tiempo)
    {
        $user = new LoginController();
        $usuario = $user->checkXamarin();
        $personalIntelisis= PersonalIntelisis::where('idpersonal', $usuario->original[0]->noempleado)->first();
        $jefe="";
        $jefe_plaza="";
        if($personalIntelisis->plaza_sup!=""){
           $jefe_plaza= PersonalIntelisis::where('plaza', $personalIntelisis->plaza_sup)->first();
           $jefe= $jefe_plaza->nombre." ".$jefe_plaza->apellidos;
        }
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
        $pdf->Cell($width, 7, Carbon::now()->format('d/m/Y'), 'BR', 0, 'C', false);
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
        $date = Carbon::parse(str_replace("/", "-", $request->start_date))->locale('es');
        $date= utf8_decode(ucfirst($date->isoFormat('dddd')).", ".$date->format('d')." de ".ucfirst($date->monthName).' de '.$date->format('Y'));
        $pdf->Cell(0, 7, $date, 'BR', 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Arial', '');
        $pdf->Cell($width, 7, '');
        $pdf->Cell(20, 7, 'Al', 'BR', 0, 'C');
        $pdf->SetFont('Arial', 'B');
        $date1 = Carbon::parse(str_replace("/", "-", $request->end_date))->locale('es');
        $date1= utf8_decode(ucfirst($date1->isoFormat('dddd')).", ".$date1->format('d')." de ".ucfirst($date1->monthName).' de '.$date1->format('Y'));
        $pdf->Cell(0, 7, $date1, 'BR', 0, 'L');
        $pdf->Ln(12);
        
        $pdf->SetFont('Arial','');
        $pdf->Cell($width,10,'Con goce de sueldo','LBTR',0,'C');
        $pdf->SetFont('Arial','B');
        $pdf->Cell($width,10,($request->type!='N')?'Si':'-','LBTR',0,'C',true);
        
        $pdf->SetFont('Arial','');
        $pdf->Cell($width,10,'Sin goce de sueldo','BTR',0,'C');
        $pdf->SetFont('Arial','B');
        $pdf->Cell($width,10,($request->type=='N')?'Si':'-','LBTR',0,'C',true);
        $pdf->Ln(12);

        $pdf->SetFont('Arial','');
        $pdf->Cell(0,7,'Motivo del permiso:','LBTR',0,'C',true);
        $pdf->Ln();

        $pdf->SetFont('Arial','B');
        $pdf->MultiCell(0,6,utf8_decode($request->reason),'LR');
        $pdf->SetFont('Arial','');
        
        $pdf->MultiCell(0,6,utf8_decode($request->observations),'LBR');
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
        
        $pdf->AddFont('FallisComing-Regular','B','fallis.php');
        $pdf->SetFont('FallisComing-Regular','B',22);
        $pdf->Cell($width*2,15,'','LR',0,'C');
        $pdf->Cell($width*2,15,'','LR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,6,"    ",'LR',0,'I');
        $pdf->SetFont('Arial','',11);
        $pdf->Cell($width*2,6,'','LR',0,'C');
        $pdf->Ln();
        $pdf->SetFont('Arial','I',7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell($width*2,2,"",'LR',0,'I');
        $pdf->Cell($width*2,2,'','LR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,2,"       ",'LR',0,'I');
        $pdf->Cell($width*2,2,'','LR',0,'C');
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell($width*2,5,utf8_decode($personalIntelisis->nombre." ".$personalIntelisis->apellidos),'LR',0,'C');
        $pdf->Cell($width*2,5,utf8_decode($jefe),'LR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,5,'','LBR',0,'C');
        $pdf->Cell($width*2,5,'','LBR',0,'C');
        $pdf->Ln();
        $pdf->Cell($width*2,7,"Firma del Empleado",'LBR',0,'C');
        $pdf->Cell($width*2,7,"Jefe Inmediato",'LBR',0,'C');
        $pdf->Ln();
        
        $pdf->SetFont('Arial','');
        $pdf->Cell($width*2,17,'','LR',0,'C');
        $pdf->Cell($width*2,17,'','LR',0,'C');
        $pdf->Ln();
        
        $pdf->Cell($width*2,15,utf8_decode($rh_nombre),'LBR',0,'C');
        $director=$this->director($personalIntelisis,$request->type,$rh_nombre,$jefe,$request->days_leave);
        
        $pdf->Cell($width*2,15,utf8_decode($director['director']),'LBR',0,'C');
        $pdf->Ln();
       
        $pdf->Cell($width*2,7,utf8_decode($this->trasformar($rh_puesto)),'LBR',0,'C');
        
        $pdf->Cell($width*2,7,utf8_decode($this->trasformar($director['puesto'])),'LBR',0,'C');
        $pdf->Ln();
        $nombre_pdf='../../../../inetpub/wwwroot/intranet/permisos_new/pdfs/'.$usuario->original[0]->usuario."_".$tiempo.".pdf";
        if($pdf->Output($nombre_pdf,"F")!=""){
            return ['status'=>'error','message'=>'Error al generar pdf'];
        }
        
        $archivo="permisos_new/pdfs/".$usuario->original[0]->usuario."_".$tiempo.".pdf";
        $solicitud=new DmiSolicitud();
        $solicitud->llave=$tiempo;
        $solicitud->pdf=$archivo;
        $solicitud->app='permisos';
        $solicitud->usuario=$usuario->original[0]->usuario;
        $solicitud->fecha=Carbon::now()->format('Y-m-d');
        $solicitud->hora=Carbon::now()->format('H:i:s');
        $solicitud->ip=$request->ip();
        $solicitud->estatus='solicitada';
        $solicitud->referencia=$usuario->original[0]->noempleado;
        $solicitud->ruta=$archivo;
        $solicitud->intelisis='no';
        $solicitud->tabla='permisos';
       
       
        if($solicitud->save()){
            if(!$this->addFirma($request,$tiempo,1,18,160,$personalIntelisis->nombre.' '.$personalIntelisis->apellidos,$personalIntelisis->emailempresa,$usuario->original[0]->usuario,'pendiente')){
                $solicitud->delete();
                return ['status'=>'error','message'=>'Error al registrar firma'];
            }
            if(!$this->addFirma($request,$tiempo,2,112,160,$jefe,$jefe_plaza->emailempresa,$usuario->original[0]->usuario,'pendiente')){
                $solicitud->delete();
                return ['status'=>'error','message'=>'Error al registrar firma'];
            }
            if(!$this->addFirma($request,$tiempo,3,18,197,$rh_nombre,$rh_correo,$usuario->original[0]->usuario,'pendiente')){
                $solicitud->delete();
                return ['status'=>'error','message'=>'Error al registrar firma'];
            }
            if($director['director']!=""){
                if(!$this->addFirma($request,$tiempo,4,112,197,$director['director'],$director['correo'],$usuario->original[0]->usuario,'pendiente')){
                    $solicitud->delete();
                    return ['status'=>'error','message'=>'Error al registrar firma'];
                }
            }
        }else{
            return ['status'=>'error','message'=>'Error al registrar solicitud'];
        }
        return ['status'=>'succes', 'message'=>'guardado correctamente'];
    }
    protected function director($personalIntelisis,$goce,$rh_nombre,$jefe,$dias ){
    
        $personal=PersonalIntelisis::where('nombre',$personalIntelisis->nombre)
        ->where('apellidos',$personalIntelisis->apellidos)
        ->where('departamento','not like','%consejo%')
        ->where('estatus','ALTA')
        ->first();
        $direcxtor="";
        $puesto="";
        $correo="";
        if ($personal) {
            $jefe_plaza= PersonalIntelisis::where('plaza', $personal->plaza_sup)->first();
            $direcxtor= $jefe_plaza->nombre." ".$jefe_plaza->apellidos;
            $puesto=$jefe_plaza->puesto;
        }
        if (($goce == "O"  || $goce == "N" && $dias > 3)  && $rh_nombre != $direcxtor && $jefe != $direcxtor) {
        }else{
            $direcxtor="";
            $puesto="";
            $correo="";
        }
        return ['director'=>$direcxtor,'puesto'=>$puesto,'correo'=>$correo];
    }
    protected function addFirma($request,$tiempo,$orden_firmas,$cordenada_firmaX,$cordenada_firmaY,$nombre,$correo,$usuario,$status){
      if($nombre!=""){
        $firma=new DmiFirma();
        $firma->llave=$tiempo;
        $firma->nombre=$nombre;
        $firma->correo=$correo;
        $firma->estatus=$status;
        $firma->ip=$request->ip();
        $firma->orden=$orden_firmas;
        $firma->X=$cordenada_firmaX;
        $firma->Y=$cordenada_firmaY;
        $firma->ancho ="28";
        $firma->usuario=$usuario;
        $firma->app="permisos";
       
        if (!$firma->save()) {
            DmiFirma::where('llave',$tiempo)->delete();
            return false;
        }
      }
        return true;
    }
    protected function trasformar($nombre){
        $firma_digital = strtolower($nombre);
        $firma_digital = ucwords($firma_digital);
        return $firma_digital;
    }
    
    protected function concept(){
        $concept=PermissionConcept::where('activo',1)->get();
        return response()->json($concept, 200);
    }
    protected function anotherConcept(){
        $concept=AnotherConceptPermission::where('activo',1)->get();
        return response()->json($concept, 200);
    }
    protected function temporaryPosition($goce, $dias, $ruta)
    {

        $query = "SELECT p.idpersonal, p.nombre, p.apellidos, p.puesto, p.emailempresa FROM intranet.puesto_temporal as t
        inner join intranet.personal_intelisis  as p on p.idpersonal= t.idPersonal where ";
        $ruta = explode("/", $ruta);
        $ruta = $ruta[1];
        if ($goce == "O") {
            $query .= " t.puesto like '%GERENTE%' AND t.departamento = 'RECURSOS HUMANOS' and t.ruta like '%$ruta%' and t.estatus = 'ALTA' limit 1";
        } else if ($goce == "S") {
            $query .= " t.puesto like '%COORDINADOR%' AND t.departamento = 'RECURSOS HUMANOS' and  t.ruta like '%$ruta%'
            and t.estatus = 'ALTA' limit 1";
        } else if ($goce == "N" && $dias < 4) {
            $query .= " t.puesto like '%COORDINADOR%' AND t.departamento = 'RECURSOS HUMANOS' and  t.ruta like '%$ruta%'
            and t.estatus = 'ALTA' limit 1";
        } else if ($goce == "N" && $dias > 3) {
            $query .= " t.puesto like '%GERENTE%' AND t.departamento = 'RECURSOS HUMANOS'  and  t.ruta like '%$ruta%' and t.estatus = 'ALTA' limit 1";
        }
        return $query;
    }
    protected function permissionCompleted(){
        $filter= $this->filterRequest('firma completada');
        return response()->json($filter, 200);
    }
    protected function permissionCancel(){
        $filter= $this->filterRequest('cancelada');
        return response()->json($filter, 200);
    }
    protected function permissionRequested(){
        $filter= $this->filterRequest('solicitada');
        return response()->json($filter, 200);
    }
    protected function  filterRequest($estatus){
        $user = Auth::user();
        $url=ENV('URL_FILES')."FPDI/archivos_pdf/";
        $filter=DB::SELECT("select p.fechasolicitud,p.goce as type, p.estatus, p.fechainicio as start_date, p.fechafin as end_date, p.firma, 
        max(f.orden) as num, max(f.estatus) as estatus_firma, max(f.fecha) as fecha_firma, max(f.hora) as hora_firma, p.idsolicitud,
        (
                    select nombre from intranet.dmi_firmas 
                    where llave = s.llave and usuario = s.usuario and app = s.app and 
                    orden = (select max(orden) from intranet.dmi_firmas 
                         where llave = s.llave and usuario = s.usuario and app = s.app 
                         and (estatus = 'rechazada' or estatus = 'completada')
                         group by llave) LIMIT 1
                ) as firmo,
                concat('".$url."',(
                    select max(pdf) from intranet.dmi_firmas 
                    where llave = s.llave and usuario = s.usuario and app = s.app 
                    and (estatus = 'rechazada' or estatus = 'completada')
                    group by llave  LIMIT 1
        ) )as archivo, p.usuario
        from intranet.solicitudes_permisos as p
        inner join intranet.dmi_solicitudes as s on
        p.firma = s.llave and p.usuario = s.usuario
        left join intranet.dmi_firmas as f on
        s.llave = f.llave and s.usuario = f.usuario and s.app = f.app 
        and (f.estatus = 'rechazada' or f.estatus = 'completada')
        where s.app = 'permisos' and p.usuario = '".$user->usuario."' and p.estatus = '".$estatus."'
        group by p.firma order by p.fechasolicitud desc
        ");
        return $filter;
    }
    protected function requestsToSing(){
        $user = Auth::user();
        $url=ENV('URL_FILES')."FPDI/archivos_pdf/";
        $user_name=$user->nombre." ".$user->apePat." ".$user->apeMat;
        $res=DB::SELECT("SELECT p.firma, p.fechasolicitud, s.app as type,
        (select nombre from intranet.dmi_firmas where s.llave = llave and s.app = app and s.usuario = usuario and orden = 1 LIMIT 1) as solicitante,
        concat('".$url."',(
                            select max(pdf) from intranet.dmi_firmas 
                            where llave = s.llave and usuario = s.usuario and app = s.app 
                            and (estatus = 'rechazada' or estatus = 'completada')
                            group by llave  LIMIT 1
                )) as archivo
         FROM intranet.dmi_firmas as f 
        inner join intranet.solicitudes_permisos as p on
         p.firma  = f.llave 
         left join intranet.dmi_solicitudes as s on
         p.firma = s.llave and p.usuario = s.usuario
        where f.estatus='enviado' and 
        (f.app='permisos' OR f.app='vacaciones') and 
        f.nombre='".$user_name."' and p.estatus='solicitada'");
        return response()->json($res, 200);
    }
    protected function rejectRequest(Request $request){
        $user = Auth::user();
        $user_name=$user->nombre." ".$user->apePat." ".$user->apeMat;
        $firma=DmiFirma::where('llave',$request->firma)->where('nombre',$user_name)->first();
        $firma->estatus="rechazada";
        if($firma->update()){
            $solicitud= requestPermission::where('firma',$request->firma)->first();
            requestPermission::where('firma',$request->firma)->update(['estatus' => 'firma rechazada']);
            $email=DmiFirma::where('llave',$request->firma)->where('orden','1')->first();
            $subject="";
            if($firma->app=="permisos"){
                $subject="Solicitud de  permiso rechazada";
            }else if($firma->app=="vacaciones"){
                $subject="Solicitud de Vacaciones rechazada";
            }
            $data=[
                "name"=>ucwords(strtolower($email->nombre)),
                "content"=>"Te informamos que tu solicitud con fecha inicio <strong>".
                Carbon::parse($solicitud->fechainicio)->format('d/m/Y')."</strong> y fecha fin<strong>".
                Carbon::parse($solicitud->fechafin)->format('d/m/Y').
                "</strong> fue rechazada por <strong>".ucwords(strtolower($user_name))."</strong>",
                "subject"=>$subject
            ];
           
            Mail::to($email->correo)
            ->send(new EmailAddFirm($data));
            return response()->json(['success' => 'Se rechazo la solicitud exitosamente.'], 200);
        }
        return response()->json(['error' => 'Error al rechazar la solicitud.'], 500);
    }
    protected function workerRequests(){
        $url=ENV('URL_FILES')."FPDI/archivos_pdf/";
        $user = Auth::user();
        $user_name=$user->nombre." ".$user->apePat." ".$user->apeMat;
        $res=DB::SELECT("SELECT p.firma, p.estatus, p.fechainicio as start_date, fechasolicitud,
        (select nombre from intranet.dmi_firmas where s.llave = llave and s.app = app and s.usuario = usuario and orden = 1 LIMIT 1) as solicitante,
        p.fechafin as end_date,
        (
                            select concat(nombre,' - ',fecha,' ', hora) from intranet.dmi_firmas 
                            where llave = s.llave and usuario = s.usuario and app = s.app and 
                            orden = (select max(orden) from intranet.dmi_firmas 
                                 where llave = s.llave and usuario = s.usuario and app = s.app 
                                 and (estatus = 'rechazada' or estatus = 'completada')
                                 group by llave) LIMIT 1
                        ) as firmo,
                        concat('".$url."',(
                            select max(pdf) from intranet.dmi_firmas 
                            where llave = s.llave and usuario = s.usuario and app = s.app 
                            and (estatus = 'rechazada' or estatus = 'completada')
                            group by llave  LIMIT 1
                )) as archivo
         FROM intranet.dmi_firmas as f
        inner join intranet.solicitudes_permisos as p on
                 p.firma  = f.llave 
                 left join intranet.dmi_solicitudes as s on
                 p.firma = s.llave and p.usuario = s.usuario
        where f.estatus='completada' and f.nombre='".$user_name."' and f.app='permisos' and f.usuario <> '".$user->usuario."'");
        return response()->json($res, 200);
    }
}
