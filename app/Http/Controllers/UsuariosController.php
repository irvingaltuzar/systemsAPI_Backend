<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SegUsuario as Usuario;
use App\Models\SegLogin as SegLogin;
use App\Models\SegSubseccion as Subseccion;
use App\Models\SegSeccion as Seccion;
use App\Models\CatTipoPermiso as TipoPermiso;
use App\Models\VwPersonalIntelisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DmiRh\DmirhPersonalTime;
use Carbon\Carbon;
use App\Models\PersonalIntelisis as PersonalIntelisis;
use App\Models\DmiRh\DmirhPersonalTimeDetail;
use App\Models\DmiControlUserSettings;

class UsuariosController extends Controller
{
    protected function getUsuarios(){
        if(Auth::check()){
        $res= Usuario::all();

        return response()->json($res, 200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }

    protected function addUsuario(Request $request){

        $datos= $this->validate(request(),[
            'nombre' => 'required',
            'apePat' => 'required',
            'apeMat' => 'required',
            'usuario' => 'required',
            'roles' => 'required',
            'borrado' => 'required',
            'permisos' => 'required'
            ]);

        if(Auth::check()){
        try {
            $arrayPermisos=json_decode($datos["permisos"]);
            $usuario= new Usuario();
            $usuario->nombre= $datos["nombre"];
            $usuario->apePat= $datos["apePat"];
            $usuario->apeMat= $datos["apeMat"];
            $usuario->usuario= $datos["usuario"];
            $usuario->password= bcrypt('OupQrqJT');
            $usuario->roles= $datos["roles"];
            $usuario->borrado= $datos["borrado"];
            $usuario->save();
            $userid=DB::getPdo()->lastInsertId();

            for ($i = 0; $i <count($arrayPermisos); $i++) {
                if( $arrayPermisos[$i]->crud!=""){
                $logincrud =new SegLogin();
                $logincrud->usuarioid=$userid;
                $logincrud->subsecId= $arrayPermisos[$i]->nSubseccion;
                $logincrud->loginUsr=$datos["usuario"];
                $logincrud->loginCrud= $arrayPermisos[$i]->crud;
                $logincrud->save();
                }
            }
            //Update usuario_ad Intelisis
            $usr= PersonalIntelisis::where("status","ALTA")->where("name",$datos["nombre"])->where(function($query) use($datos) {
                $query->where("last_name",$datos["apePat"]." ".$datos["apeMat"])
                ->orWhere("last_name",$datos["apePat"]."  ".$datos["apeMat"]);
            })->first();

            $usr->usuario_ad=$datos["usuario"];
            $usr->save();

            //creacion Horario
            $personaltime = new DmirhPersonalTime();
            $personaltime->user = $datos["usuario"];
            $personaltime->dmirh_cat_time_status_id = 1;
            $personaltime->start_date = Carbon::now();
            $personaltime->approved_by = "Sistemas";
            $personaltime->approved_date =Carbon::now();
            $personaltime->active =1;
            $personaltime->save();
            $personaltimeId=DB::getPdo()->lastInsertId();

           $jor='Lunes,Martes,Miercoles,Jueves,Viernes';
            $arrJornada= explode(",",$jor);

            foreach ( $arrJornada as $value) {
              $personaltimeDetail = new DmirhPersonalTimeDetail();
              $personaltimeDetail->dmirh_personal_time_id= $personaltimeId;

              if($value =="Lunes"){
              $personaltimeDetail->week_day= 1;
              $personaltimeDetail->entry_hour= "09:00";
              $personaltimeDetail->exit_food_hour= "14:00";
              $personaltimeDetail->entry_food_hour= "15:00";
              $personaltimeDetail->exit_hour= "18:00";
              }else if($value =="Martes"){
                $personaltimeDetail->week_day= 2;
                $personaltimeDetail->entry_hour= "09:00";
                $personaltimeDetail->exit_food_hour= "14:00";
                $personaltimeDetail->entry_food_hour= "15:00";
                $personaltimeDetail->exit_hour= "18:00";
              }else if($value =="Miercoles"){
                $personaltimeDetail->week_day= 3;
                $personaltimeDetail->entry_hour= "09:00";
                $personaltimeDetail->exit_food_hour= "14:00";
                $personaltimeDetail->entry_food_hour="15:00";
                $personaltimeDetail->exit_hour="18:00";
              }else if($value =="Jueves"){
                $personaltimeDetail->week_day= 4;
                $personaltimeDetail->entry_hour="09:00";
                $personaltimeDetail->exit_food_hour= "14:00";
                $personaltimeDetail->entry_food_hour= "15:00";
                $personaltimeDetail->exit_hour= "18:00";
              }else if($value =="Viernes"){
                $personaltimeDetail->week_day= 5;
                $personaltimeDetail->entry_hour= "09:00";
                $personaltimeDetail->exit_food_hour= "14:00";
                $personaltimeDetail->entry_food_hour= "15:00";
                $personaltimeDetail->exit_hour="18:00";
              }else if($value =="Sabado"){
                $personaltimeDetail->week_day= 6;
                $personaltimeDetail->entry_hour= "09:00";
                $personaltimeDetail->exit_food_hour= "14:00";
                $personaltimeDetail->entry_food_hour="15:00";
                $personaltimeDetail->exit_hour= "18:00";
              }else if($value =="Domingo"){
                $personaltimeDetail->week_day= 7;
                $personaltimeDetail->entry_hour= "09:00";
                $personaltimeDetail->exit_food_hour= "14:00";
                $personaltimeDetail->entry_food_hour="15:00";
                $personaltimeDetail->exit_hour= "18:00";
              }
              $personaltimeDetail->save();
            }
            } catch (Exception $th) {
            return response()->json(['error'=>$th],500);
            }
         return response()->json(['success'=>'Usuario agregado Correctamente.'],200);
    }else{

    return response()->json(['error'=>'No tienes Sesion'],200);
    }
  }

    protected function EditarUsuario(Request $request){

        $datos= $this->validate(request(),[
            'usuarioId' => 'required',
            'nombre' => 'required',
            'apePat' => 'required',
            'apeMat' => 'required',
            'usuario' => 'required',
            'roles' => 'required',
            'borrado' => 'required',
            'permisos' => 'required'
            ]);

        if(Auth::check()){

        try {
            $arrayPermisos=json_decode($datos["permisos"]);
            $usuario= Usuario::find($datos["usuarioId"]);
            $usuario->nombre= $datos["nombre"];
            $usuario->apePat= $datos["apePat"];
            $usuario->apeMat= $datos["apeMat"];
            $usuario->usuario= $datos["usuario"];
            $usuario->password= bcrypt('OupQrqJT');
            $usuario->roles= $datos["roles"];
            $usuario->borrado= $datos["borrado"];
            $usuario->save();

            for ($i = 0; $i <count($arrayPermisos); $i++) {
                $ologin=SegLogin::where('subsecId',$arrayPermisos[$i]->nSubseccion)->where('usuarioId',$datos["usuarioId"])->first();
                if( $arrayPermisos[$i]->crud!=""){
                    if($ologin){
                        $logincrud =SegLogin::find($ologin->loginId);
                        $logincrud->usuarioid=$datos["usuarioId"];
                        $logincrud->subsecId= $arrayPermisos[$i]->nSubseccion;
                        $logincrud->loginUsr=$datos["usuario"];
                        $logincrud->loginCrud= $arrayPermisos[$i]->crud;
                        $logincrud->save();
                    }
                    else{
                        $logincrud =new SegLogin();
                        $logincrud->usuarioid=$datos["usuarioId"];
                        $logincrud->subsecId= $arrayPermisos[$i]->nSubseccion;
                        $logincrud->loginUsr=$datos["usuario"];
                        $logincrud->loginCrud= $arrayPermisos[$i]->crud;
                        $logincrud->save();
                    }
                }else{
                    if($ologin){
                    SegLogin::where('subsecId',$arrayPermisos[$i]->nSubseccion)->where('usuarioId',$datos["usuarioId"])->delete();
                    }
                }
                // $loginid=SegLogin::where("subsecId",$arrayPermisos[$i]->nSubseccion)->where("usuarioId",$datos["usuarioId"]);

            }
            } catch (\Throwable $th) {
            return response()->json(['error'=>'Error al modificar.'],500);
            }
        return response()->json(['success'=>'Usuario modificado Correctamente.'],200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
    }
    }

    protected function getSubsecciones(){
        // if(Auth::check()){
        // $res= DB::select('select * from seg_seccion as a left join seg_subseccion as b on a.secId=b.secId where b.mostrar!=0 order by a.secOrden,b.subsecOrden');
        $res=Seccion::with("seg_items")->leftjoin("seg_subseccion","seg_seccion.secId","=","seg_subseccion.secId")->where("seg_subseccion.mostrar","<>",0)
        ->orderBy("seg_seccion.secOrden")->orderBy("seg_subseccion.subsecOrden")->get();


        return response()->json($res, 200);
    // }else{

    //     return response()->json(['error'=>'No tienes Sesion'],200);
    //     }
    }

    protected function getSecciones(){
        if(Auth::check()){
        $res= Seccion::orderBy('secOrden')->get();

        return response()->json($res, 200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }
    protected function getSeccionesPermisos(){
        if(Auth::check()){
        // $res= DB::select('select * from seg_seccion as a left join seg_subseccion as b on a.secId=b.secId where b.mostrar!=0 and b.[public]=0 order by a.secOrden,b.subsecOrden');
            // $res= DB::table("seg_seccion")
            // ->leftjoin("seg_subseccion", "seg_seccion.secId", '=',"seg_subseccion.secId")->where("seg_subseccion.mostrar",1)->where("seg_subseccion.public",0)
            // ->orderBy("seg_seccion.secOrden")->get();
            $res= SubSeccion::with(["seg_seccion","seccion_top"])->where("mostrar",1)->where("public", 0)->orderBy("secId")->get();

        return response()->json($res, 200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }

    protected function getTipoPermisos(){
        if(Auth::check()){
        $res= TipoPermiso::where('borrado',0)->get();



        return response()->json($res, 200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }
    protected function getTipoPermisosAll(){
        if(Auth::check()){
        $res= TipoPermiso::all();



        return response()->json($res, 200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }

    protected function EditarTipoPermiso(Request $request){


        $datos= $this->validate(request(),[

            'tipoPermisoId' => 'required',
            'tipoPermisoDesc' => 'required',
            'tipoPermisoVal' => 'required',
            'borrado' => 'required',
            ]);

          if(Auth::check()){

              try {
                $permiso= TipoPermiso::find($datos["tipoPermisoId"]);

                $permiso->tipoPermiso_Desc= $datos["tipoPermisoDesc"];
                $permiso->tipoPermiso_Val= $datos["tipoPermisoVal"];
                $permiso->borrado= $datos["borrado"];
                $permiso->save();
              } catch (\Throwable $th) {
                return response()->json(['error'=>'Error al modificar.'],500);
              }


             return response()->json(['success'=>'Tipo Permiso Modificado Correctamente.'],200);
      }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
      }

      protected function addTipoPermiso(Request $request){

        $datos= $this->validate(request(),[

            'tipoPermisoDesc' => 'required',
            'tipoPermisoVal' => 'required',
            'borrado' => 'required',
          ]);

        if(Auth::check()){

            try {
                $permiso= new TipoPermiso();
                $permiso->tipoPermiso_Desc= $datos["tipoPermisoDesc"];
                $permiso->tipoPermiso_Val= $datos["tipoPermisoVal"];
                $permiso->borrado= $datos["borrado"];
                $permiso->save();
            } catch (\Throwable $th) {
              return response()->json(['error'=>'Error al insertar.'],500);
            }


           return response()->json(['success'=>'Tipo Permiso agregado Correctamente.'],200);
    }else{

      return response()->json(['error'=>'No tienes Sesion'],200);
      }
    }

    protected function getSubseccionesUsuario(Request $request){
        if(Auth::check()){
        $datos= $this->validate(request(),[
            'usuarioId' => 'required',
            ]);

            try {
                $res= SegLogin::where('usuarioId',$datos["usuarioId"])->get();
            } catch (\Throwable $th) {
                return response()->json(['error'=>'Error al consultar.'],500);
            }

        return response()->json($res, 200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }
    protected function getUsuariosEmail(){
        if(Auth::check()){
        $res= VwPersonalIntelisi::all();

        return response()->json($res, 200);
    }else{

        return response()->json(['error'=>'No tienes Sesion'],200);
        }
    }

    public function getListUserSettings($_rfc){
        if(Auth::check()){

            $user_settings = DmiControlUserSettings::where('module','like','user_settings')
                                                    ->where('value',$_rfc)
                                                    ->get();

            return response()->json([
                'success' => 1,
                'data' => $user_settings,
                'message' => ""
            ],200);
        }else{
            return response()->json([
                'success' => 0,
                'message' => "No has iniciado sesión"
            ],200);
        }
    }

    public function excludeIncidentProcess(Request $request){

        $setting = DmiControlUserSettings::where('key','incident_process-exclude_user-rfc')
                                            ->where('value',$request->rfc)
                                            ->first();

        if($setting != null){
            $setting->data = $request->status;
            $setting->save();
        }else{
            $setting = new DmiControlUserSettings();
            $setting->module = "user_settings";
            $setting->key = "incident_process-exclude_user-rfc";
            $setting->value = $request->rfc;
            $setting->data = $request->status;
            $setting->save();
        }
        
        

        return response()->json([
            'success' => 1,
            'data' => $setting,
            'message' => ""
        ],200);

    }


}
