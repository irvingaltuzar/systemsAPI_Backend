<?php

namespace App\Http\Controllers\Auth;

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
                'user'=>Auth::user(),
                'access_token' =>   $tokenResult,
                  200]);
          } else{
                      throw ValidationException::withMessages([
                      'usuario' => ['Tu usuario no existe en nuestra base de Datos']
                  ]);
          }
      }
      } catch (\Throwable $th) {
               throw ValidationException::withMessages([
                  'usuario' => ['Las crendeciales son incorrectas, intentalo nuevamente']
              ]);

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

    
  public function ShowLogin()
  {
      return view('login');
  }  

  public function registration()
  {
      return view('registration');
  }
  

  public function postLogin(Request $request)
  {
    $credentials= $this->validate(request(),[
      'usuario' => 'required|string',
      'password' => 'required|string',
      ]);

      if (Auth::attempt($credentials)) {
          // Authentication passed...
          return redirect()->route('index');
      }
      return back()
      ->withErrors(['usuario'=>trans('auth.failed')])
      ->withInput(request(['usuario']));
  }

  public function postRegistration(Request $request)
  {  
      request()->validate([
      'name' => 'required',
      'email' => 'required|email|unique:users',
      //'contact_number' => 'required|contact_number|unique:users',
      'password' => 'required|min:8',
      ]);
      
      $data = $request->all();

      $check = $this->create($data);
    
      return Redirect::to("index")->withSuccess('Great! You have Successfully loggedin');
  }
  
  public function dashboard()
  {

    if(Auth::check()){
      return view('index');
    }
     return Redirect::to("login")->withSuccess('Opps! You do not have access');
  }

public function create(array $data)
{
 return User::create([
   'name' => $data['name'],
   'email' => $data['email'],
   'password' => Hash::make($data['password'])
 ]);
}

public function logout() {
      Session::flush();
      Auth::logout();

      return Redirect::to("login");

      
  }
}