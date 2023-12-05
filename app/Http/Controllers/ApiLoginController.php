<?php

namespace App\Http\Controllers;

use App\Ldap\UserLdap;
use App\Models\PersonalIntelisis;
use Error;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ServerRequestInterface;

class ApiLoginController extends Controller
{
	public function username()
	{
		return 'username';
	}

	public function login(Request $request)
	{
		$credentials = ['usuario' => $request->temp_user, 'password' => 'OupQrqJT'];

		if (!!$request->is_login) {
			return response()->json(Auth::user(), 200);
		} else {
			try {
				if (Auth::attempt($credentials)){
					$this->setLoginAttr($request->id);
					return response()->json(Auth::user(), 200);
				} else{
					return response()->json([
						'usuario' => ['Las credenciales son incorrectas, intentalo nuevamente']
					]);
				}
			} catch (Error $th) {
				return response()->json([
					'usuario' => ['Las credenciales son incorrectas, intentalo nuevamente']
				]);
			}
		}
	}

	public function checkToken(String $token)
	{
		$is_validate = DB::connection('intranet_sqlsrv')->table('temp_sessions')
					->where(['personal_token' => $token])
					->where('is_expired', false)
					->whereNull('deleted_at')
					->first();

		if ($is_validate) {
			return response()->json([
				'is_validate' => true,
				'data_json' => $is_validate
			]);
		} else {
			return response()->json([
				'is_validate' => false
			]);
		}
	}

	public function setLoginAttr(int $id)
	{
		$updated = DB::connection('intranet_sqlsrv')->table('temp_sessions')
					->where(['id' => $id])
					->update([
						'is_login' => true
					]);

		return $updated;
	}
}
