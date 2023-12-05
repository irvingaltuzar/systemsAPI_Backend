<?php

namespace App\Http\Controllers\Xamarin\Auth;

use Illuminate\Http\Request;
use Validator,Redirect,Response;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Session;

use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
  public function __construct(){
    $this->middleware('guest',['only'=>'ShowLogin']);
  }
  public function username() {
    return 'usuario';
}
public function password() {
    return 'password';
}
  /**
     * Inicio de sesión y creación de token
     */
    public function login(Request $request)
    {

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
        
               $user = Auth::user();
              $tokenResult = $user->createToken('Personal Access Token')->accessToken;
              return response()->json([
                'usuario'=>$user,
                'accessToken' => $tokenResult,
                ]);
          } else{
                      throw ValidationException::withMessages([
                      'usuario' => ['Tu usuario no existe en nuestra base de Datos']
                  ]);
          }
      }
      } catch (\Throwable $th) {
        return response()->json(null);
             
    }
  }

  
    /**
     * Obtener el objeto User como json
     */
    public function user(Request $request)
    {
      $user = Auth::user();
        return response()->json($user);
    }


    /**
    *Funcion Cerrar Sesion Laravel
    */
    public function logout(Request $request) {
      $user = Auth::user();
     $user->token()->revoke();
 
      return response()->json([
          'Successfully logged out'
      ]);
    }
}