<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalIntelisis as PersonalIntelisis;

class LoginController extends Controller
{



    public function check()
    {
        if(Auth::check()){
            $id=Auth::id();
            $usuario =auth()->user()->usuario;
            // $sql=DB::select('(select `a`.`usuarioId` AS `usuarioId`,`b`.`ruta`,`b`.`id` AS `usuarioIdIntranet`,`a`.`nombre` AS `nombre`,`a`.`apePat` AS `apePat`,`a`.`apeMat`
            // AS `apeMat`,`a`.`usuario` AS `usuario`,`a`.`password` AS `password`,`a`.`roles` AS `roles`,`a`.`borrado` AS `borrado`
            // ,`c`.`ubicacion` AS `ubicacion`,`c`.`idpersonal` AS `noempleado`,`c`.`personal_intelisis_id` from ((`alfa`.`seg_usuarios` `a` left join `intranet`.`usuario` `b`
            //  on((`a`.`usuario` = (`b`.`usuario` collate utf8_general_ci)))) left join `intranet`.`personal_intelisis` `c`
            //  on(((`b`.`nombre` = `c`.`nombre`) and (`b`.`apellidos` = `c`.`apellidos`)))) where (`c`.`estatus` like "ALTA" and `a`.`usuarioId`='. $id.' ))
            //  ');

            //Get Info Table personal Intelisis with security_users
            $res=  DB::table('personal_intelisis')
            ->join('seg_usuarios', 'personal_intelisis.usuario_ad', '=', 'seg_usuarios.usuario')->where("personal_intelisis.usuario_ad",$usuario)
            ->where("personal_intelisis.status","ALTA")->where("seg_usuarios.borrado",0)->get();

            // PersonalIntelisis::where("usuario_ad",$usuario)->where("status","ALTA")->get();

            return response()->json($res, 200);

        }  else{
            return response()->json(null);
        }
    }

    public function checkXamarin()
    {
        if(Auth::check()){
            $id=Auth::id();
            $usuario =auth()->user()->usuario;
            $sql=DB::select('(select `a`.`usuarioId` AS `usuarioId`,`b`.`ruta`,`b`.`id` AS `usuarioIdIntranet`,`a`.`nombre` AS `nombre`,`a`.`apePat` AS `apePat`,`a`.`apeMat`
            AS `apeMat`,`a`.`usuario` AS `usuario`,`a`.`password` AS `password`,`a`.`roles` AS `roles`,`a`.`borrado` AS `borrado`
            ,`c`.`ubicacion` AS `ubicacion`,`c`.`idpersonal` AS `noempleado`,`c`.`personal_intelisis_id` from ((`alfa`.`seg_usuarios` `a` left join `intranet`.`usuario` `b`
             on((`a`.`usuario` = (`b`.`usuario` collate utf8_general_ci)))) left join `intranet`.`personal_intelisis` `c`
             on(((`b`.`nombre` = `c`.`nombre`) and (`b`.`apellidos` = `c`.`apellidos`)))) where (`c`.`estatus` like "ALTA" and `a`.`usuarioId`='. $id.' ))
             ');


            // PersonalIntelisis::where("usuario_ad",$usuario)->where("status","ALTA")->get();

            return response()->json($sql, 200);

        }  else{
            return response()->json(null);
        }
    }

    public function check_Permisos(Request $request)
    {
        if(Auth::check()){
            $nom_usuario=auth()->user()->usuario;

            //Query get Info permission of user in table seg_seccion/subseccions Privates
            // $sql=DB::select('select a.loginCrud,a.subsecId,b.subsecOrden,b.subsecDesc,b.subsecDenegado,b.tablaDatos,b.mostrar,
            // c.secid,c.secDesc,c.secOrden from seg_login as a inner join seg_subseccion as b on a.subsecId=b.subsecId
            // inner join seg_seccion as c on b.secId=c.secId where b.mostrar=1 and a.loginUsr=?',[$nom_usuario]);
            $sql=DB::select('select a.loginCrud,a.subsecId,b.subsecOrden,b.subsecDesc,b.subsecDenegado,b.tablaDatos,b.mostrar
            from seg_login as a inner join seg_subseccion as b on a.subsecId=b.subsecId
           where b.mostrar=1 and a.loginUsr=?',[$nom_usuario]);
            //Query get Info permission of user in table seg_seccion/subseccions publics it always shows
            $sql2=DB::select('select b.subsecId,b.subsecOrden,b.subsecDesc,b.subsecDenegado,b.tablaDatos,b.mostrar, c.secid,c.secDesc,c.secOrden
			from seg_subseccion as b inner join seg_seccion as c on b.secId=c.secId
			where b.mostrar=1 and b.[public]=1');
            array_push($sql,$sql2);


            return response()->json($sql,200);
        }  else{
            return response()->json(null);
        }
    }

    public function loginintra(Request $request)
    {
        if(Auth::viaRemember()){
            return response()->json(Auth::user(), 200);
        }  else{
            return response()->json(null);
        }
    }
    public function username() {
        return 'usuario';
    }
    public function password() {
        return 'password';
    }


    protected function login(Request $request) {
        $credentials = $request->only($this->username(), $this->password());
        $username = $credentials[$this->username()];
        $password = $credentials[$this->password()];

        $user_format = env('LDAP_USER_FORMAT');
        $userdn = sprintf($user_format, $username);

        $servidor_LDAP = env('LDAP_HOSTS');
        $conectado_LDAP = ldap_connect($servidor_LDAP);
        ldap_set_option($conectado_LDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($conectado_LDAP, LDAP_OPT_REFERRALS, 0);
    try {

        if( ldap_bind($conectado_LDAP,$username ."@".  $user_format, $password)){


			if (Auth::attempt(['usuario' => $username, 'password' => 'OupQrqJT', 'borrado' => 0])){
                // if (Auth::attempt(['usuario' => "lizeth.ramirez", 'password' => 'OupQrqJT', 'borrado' => 0])){
                        return response()->json(Auth::user(), 200);
                    } else{
                        throw ValidationException::withMessages([
                        'usuario' => ['Tu usuario no existe en nuestra base de Datos']
                    ]);
            }
        }
        } catch (\Throwable $th) {
                 throw ValidationException::withMessages([
                    'usuario' => ['Las credenciales son incorrectas, intentalo nuevamente']
                ]);

}

}

public function infouser(Request $request)
{
  $user = Auth::user();

    return response()->json($user);
}
public function logout(Request $request)
{
     Auth::logout();
    return response()->json(null);
}

}
