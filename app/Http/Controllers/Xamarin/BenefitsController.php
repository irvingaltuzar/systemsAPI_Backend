<?php

namespace App\Http\Controllers\Xamarin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Models\Intranet\Benefit;
use Carbon\Carbon;

use App\Models\Intranet\PersonalIntelisis;
use App\Models\PersonalIntelisis as persona;

class BenefitsController extends Controller
{
    protected function index(){
        $user = new LoginController();
        $usuario = $user->checkXamarin();
        $url=ENV('URL_FILES')."img/beneficios/";
        $usuario_ruta = explode("/",$usuario->original[0]->ruta);
        $usuario_ruta = $usuario_ruta[count($usuario_ruta)-1];
        $res=Benefit::select('idbeneficio','nombre','imagen','portada','archivos','fecha_pub')->whereRaw("(tipo = 'publico' or 
        (tipo = 'interno' AND ruta = '".$usuario_ruta."') or 
        (tipo = 'negocio' AND privacidad LIKE '%".$usuario_ruta."%'))")->get();
        foreach ($res as $key => $value) {
            if($value->archivos!=""&&$value->archivos!=null){
                $value->archivos=$url.$value->archivos;
            }
            Carbon::setUTF8(true);
            Carbon::setLocale(config('app.locale'));
            setlocale(LC_TIME, config('app.locale'));
            $fecha = Carbon::parse($value->fecha_pub)->format('d')." ".
            ucfirst(Carbon::parse($value->fecha_pub)->formatLocalized('%B')).
            " ".Carbon::parse($value->fecha_pub)->format('Y') ;
            $value->fecha_pub=$fecha;
            $value->imagen=$url.$value->imagen;
            $value->portada=$url.$value->portada;
        }
         return response()->json($res, 200);
     }
     
}
