<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Rol;

class VerifyBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {


        //$get_bearer_token = $request->
        // bearer Token => \QYR;1FIdt,088V;rLqP#intranet#Greca
        // bearer Token-Codificado en base64 => XFFZUjsxRklkdCwwODhWO3JMcVAjaW50cmFuZXQjR3JlY2E=

        $get_bearer_token = $request->server('HTTP_AUTHORIZATION');
        //return dd($get_bearer_token);
        if(strlen($get_bearer_token) > 0){
            $bearer_token = explode(" ",$get_bearer_token);
            $type_auth = $bearer_token[0];
            $token = $bearer_token[1];
            
            // se valida que el tipo de authentificación sea por Bearer token
            if($type_auth == "Bearer" || $type_auth == "bearer"){
                // Se valida que si pertenezca a el token
                //return dd($token,base64_encode("\QYR;1FIdt,088V;rLqP#intranet#Grecas"));
                if($token == base64_encode("\QYR;1FIdt,088V;rLqP#intranet#Grecas")){ 
                    return $next($request);
                }else{
                    return response()->json(['message' => 'Token Bearer incorrecto.'], 401);
                }
            }else{
                return response()->json(['message' => 'Error de tipo autentificación.'], 403);
            }
        }else{
            return response()->json(['message' => 'Este usuario no tiene acceso.'], 403);
        }
        
        //$get_bearer_token = $request->server('HTTP_AUTHORIZATION');


        //return dd($request->server('HTTP_AUTHORIZATION'));


        /* if(Auth::check()){
            // Revisamos que cuente con el rol para acceder al admin
            if(Session::has('permission')){
                $permission = Session::get('permission.permissions');
                if(sizeof($permission) > 0){
                    return $next($request);
                }else{
                    return redirect()->route('home');
                }
            }else{
                return redirect()->route('home');
            }
        }else{
            return redirect()->route('login');
        } */
    }
}
