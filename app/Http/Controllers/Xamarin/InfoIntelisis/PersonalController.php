<?php

namespace App\Http\Controllers\Xamarin\InfoIntelisis;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use App\Models\Intranet\PersonalIntelisis;

class PersonalController extends Controller
{
    protected function getPersonal(){
        $user = new LoginController();
        $usuario = $user->checkXamarin();
        $res= PersonalIntelisis::where('idpersonal',$usuario->original[0]->noempleado)->get();
        return response()->json($res);
    }
}
