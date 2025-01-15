<?php

namespace App\Http\Controllers\Alfa\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\VwDmiPersonalPlaza;
use App\Models\DmiRh\DmirhCatTypeJustification as TypeJustification;
use App\Models\DmiRh\DmirhPersonalJustification as Justification;
use App\Models\PersonalIntelisis;
use App\Models\File as MFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\SendEmailService;
use App\Services\IntelisisSenderService;
use App\Http\Controllers\NotificationCenterController;
use App\Repositories\GeneralFunctionsRepository;

class JustificationController extends Controller
{

  public function __construct(SendEmailService $sendEmail, IntelisisSenderService $intelisisService, NotificationCenterController $notificationCenter,
                              GeneralFunctionsRepository $_GeneralFunctionsRepository)
    {
        $this->sendEmail = $sendEmail;
        $this->intelisisService = $intelisisService;
        $this->notificationCenter= $notificationCenter;
        $this->GeneralFunctionsRepository = $_GeneralFunctionsRepository;

    }
    protected function addJustification(Request $request){
        $datos= $this->validate(request(),[
        
            // 'user' => 'required', 
            'description' => 'required', 
            'date' => 'required', 
            'type_id' => 'required', 
            ]);

            $justification= new Justification();

            if($request->file('file')){

            $uploaded_file = $request->file('file');
            $file_name = $uploaded_file->getClientOriginalName();

            if( Storage::disk("Justifications")->putFileAs("/", $uploaded_file, $file_name)){
              $justification->file= $file_name;
            }
          }
          $justification->description= $datos["description"];
          $justification->type_id= $datos["type_id"];
          $justification->user= $request["user"];
          $justification->date= $datos["date"];
          $justification->status= 1;
          $justification->approved_by=auth()->user()->usuario;
          $justification->save();

          return response()->json(['success'=>'Justification se ha creado correctamente.'],200);
    }

    protected function addJustificationUser(Request $request){
      $datos= $this->validate(request(),[
      
          // 'user' => 'required', 
          'description' => 'required', 
          'date' => 'required', 
          'type_id' => 'required', 
          ]);

          $justification= new Justification();

          if($request->file('file')){

          $uploaded_file = $request->file('file');
          $file_name = $uploaded_file->getClientOriginalName();

          if( Storage::disk("Justifications")->putFileAs("/", $uploaded_file, $file_name)){
            $justification->file= $file_name;
          }
        }
        $justification->description= $datos["description"];
        $justification->type_id= $datos["type_id"];
        $justification->user= auth()->user()->usuario;
        $justification->date= $datos["date"];
        $justification->save();

        $top=PersonalIntelisis::where("usuario_ad",auth()->user()->usuario)->where("status","ALTA")->first();

        $boss=PersonalIntelisis::where("plaza_id",$top->top_plaza_id)->where("status","ALTA")->first();

        $this->notificationCenter->addNotificationCenter(
          $boss->usuario_ad,
          "Solicitud aprobacion de justificacion",
      "Se necesita tu aprobación para la solicitud de justificacion del usuario ".auth()->user()->usuario.", consulta para mas detalles...",
      "notification",
      "authorisations",
      "sign_request","media");

      $data = [
        'data' =>[
          "folio" => $justification->id,
          'subject' => "Solicitud de Justificación",
          'collaborator_name' => $top->full_name,
          'boss_name' => $boss->full_name,
        ],
        'module' => "justification",
        'to_email' => $boss->email,
      ];
      $this->sendEmail->justificationRequestNotification($data);

        return response()->json(['success'=>'Justification se ha creado correctamente.'],200);
  }
  protected function autorizarJustification(Request $request){

      $justification= Justification::find($request["id"]);

      $this->notificationCenter->addNotificationCenter(
        $justification->user,
        "Justificación aprobada",
          "Tu justificación No. ".$request["id"]." ha sido aprobada, consulta para mas detalles...",
          "notification",
          "SolicitudJustificacion",
          "approved_request","media");

      $justification->approved_by= auth()->user()->usuario;
      $justification->status= 1;
      $justification->save();
     

    return response()->json(['success'=>'Justificación Autorizado Correctamente.'],200);
  }

  protected function rechazarJustification(Request $request){

    $justification= Justification::find($request["id"]);

    $this->notificationCenter->addNotificationCenter(
      $justification->user,
      "Justificación rechazada",
  "Tu justificación No. ".$request["id"]." ha sido rechazada, consulta para mas detalles...",
  "notification",
  "SolicitudJustificacion",
  "cancel_request","media");

    $justification->approved_by= auth()->user()->usuario;
    $justification->status= 0;
    $justification->save();

  return response()->json(['success'=>'Justificación Rechazado Correctamente.'],200);
}
    protected function addTypeJustification(){
        $datos= $this->validate(request(),[
        
            'description' => 'required', 
            ]);
      
          if(Auth::check()){    
      
              try {
                $type= new TypeJustification();
           
             $type->description= $datos["description"];
             $type->save();
    
              } catch (\Throwable $th) {
                return response()->json(['error'=>'Error al insertar.'],500);
              }
            
          
             return response()->json(['success'=>' Justification agregado Correctamente.'],200);
      }else{
      
        return response()->json(['error'=>'No tienes Sesion'],500);
        }
    }

    protected function getTypeJustification(){
      if(Auth::check()){
        $res= TypeJustification::all();
            
        return response()->json($res, 200); 
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    
    }

    protected function getMyJustifications(){
      if(Auth::check()){
        $res= Justification::with("dmirh_cat_type_justification")->where("user",auth()->user()->usuario)->orderBy("date","desc")->get();
            
        return response()->json($res, 200); 
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    
    }

    protected function getJustificationsMyPersonal(){
      if(Auth::check()){
          $arr=[];
          /* $res = PersonalIntelisis::with('commanding_staff')->where("usuario_ad",Auth::user()->usuario)->where('status', '!=', "BAJA")->get();
         $this->encuentraParents($res,$salida,null); */
         //$salida = PersonalIntelisis::where('top_plaza_id',Auth::user()->personal_intelisis->plaza_id)->where('status', '!=', "BAJA")->get();
         $commanding_staff = $this->GeneralFunctionsRepository->getCommandingStaff(auth()->user()->personal_intelisis->plaza_id,'all_fields');
         if(count($commanding_staff)>0){
        foreach($commanding_staff as $pers){
          $res2= Justification::with("dmirh_cat_type_justification")->where("user",$pers->usuario_ad)->where("approved_by",null)->orderBy("date","desc")->get();
          if(count($res2)>0){
          foreach($res2 as $obJust){
            $arr[]=$obJust;
          }
        }
        }
      }
        return response()->json($arr, 200); 
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    
    }
    protected function getTypeJustificationActive(){
      if(Auth::check()){
        $res= TypeJustification::where("deleted",0)->get();
            
        return response()->json($res, 200); 
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    
    }


    protected function updateTypeJustification(){
        $datos= $this->validate(request(),[
          
          'id' => 'required', 
          'description' => 'required', 
          'deleted' => 'required', 
          ]);
    
        if(Auth::check()){    
    
          try {
              $type= TypeJustification::find($datos["id"]);
         
            $type->description= $datos["description"];
            $type->deleted= $datos["deleted"];
            $type->save();
  
            } catch (\Throwable $th) {
              return response()->json(['error'=>'Error al modificar.'],500);
            }
          
        
           return response()->json(['success'=>'Justification Modificado Correctamente.'],200);
      }else{
      
        return response()->json(['error'=>'No tienes Sesion'],200);
        }
      
      }

     public $salida = array();
      protected function getCommanding_staff(){
        if(Auth::check()){
            $res = PersonalIntelisis::with('staffall')->where("usuario_ad",Auth::user()->usuario)->where('status', '!=', "BAJA")->get();
            // ->where(function ($query) {
            //   $query->where('status', '!=', "BAJA")
            //         // ->orWhere('status', '=', null);
            // })->get();
           $this->encuentraParents($res,$salida,null);
          // $res2= $this->recurPersonal($res);
            return response()->json($salida, 200); 
        }else{
            return response()->json(null, 500); 

        }
    }

    function encuentraParents($entrada, &$salida, $padre) {
      // para cada elemento del array 
      foreach($entrada as $valor) {
          // añade una entrada al array de salida indicando su id y el de su padre
          if(str_contains($valor["position_company"], 'DIRECTOR') || str_contains($valor["position_company_full"], 'DIRECTOR')){
            $salida[] = $valor;
          }
          if($padre!=null ){
            if(isset($valor["plaza_id"]) && isset($valor["personal_id"])){
              $salida[] = $valor;

            }
      }
          // si el elemento tiene children
          if (isset($valor["staffall"]) ) {
              // procesa los hijos recursivamente indicando el id del padre
             $this->encuentraParents($valor["staffall"], $salida, $valor["plaza_id"] );
          }
      }
  }

    public function getNumberJustification(Request $request){

        $user = PersonalIntelisis::where('rfc',$request->rfc)->where('status','alta')->first();

        if($user != null){
            $justifications = Justification::join('cat_type_justification','cat_type_justification.id','=','dmirh_personal_justification.type_id')
                                            ->where('cat_type_justification.description','Entrada fuera de Horario')
                                            ->whereNull('cat_type_justification.deleted_at')
                                            ->whereNull('dmirh_personal_justification.deleted_at')
                                            ->where('dmirh_personal_justification.user',$user->usuario_ad)
                                            ->whereMonth('date',Carbon::parse($request->date)->format('m'))
                                            ->select('dmirh_personal_justification.*')
                                            ->count();

            
            return ["number_justifications" => $justifications];
        }else{
            return ["number_justifications" => 0];
        }
        
        

    }   

}
