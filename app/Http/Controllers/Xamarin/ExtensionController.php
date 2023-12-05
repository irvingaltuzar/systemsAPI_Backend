<?php

namespace App\Http\Controllers\Xamarin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PersonalIntelisis;
use App\Models\Intranet\PersonalIntelisis as person2;
use Illuminate\Support\Facades\DB;

class ExtensionController extends Controller
{
    protected function index(){
       $res=PersonalIntelisis::select(DB::raw('TRIM(CONCAT(name," ", last_name)) AS fullname'),'location as ubicacion',
       DB::raw('TRIM(position_company) as puesto') ,'deparment  as departamento','extension', 'email as emailempresa')->
         where('status','ALTA')->whereNotNull('last_name')->get();
        
        return response()->json($res, 200);
    }
    // protected function temporal(){
    //     $person2=person2::where('estatus', 'alta')->get();
    //     foreach ($person2 as $value) {
    //         $p = new PersonalIntelisis();
    //         $p->personal_id=$value->idpersonal;
    //         $p->name=$value->nombre;
    //         $p->last_name=$value->apellidos;
    //         $p->birth=$value->fechanacimiento;
    //         $p->sex=$value->genero;
    //         $p->email=$value->emailempresa;
    //         $p->extension=$value->extension;
    //         $p->photo=$value->foto;
    //         $p->position_company=$value->puesto;
    //         $p->position_company_full=$value->puesto_especifico;
    //         $p->deparment=$value->departamento;
    //         $p->date_admission=$value->fechaingreso;
    //         $p->antiquity_date=$value->fechaantiguedad;
    //         $p->location=$value->ubicacion;
    //         $p->company_name=$value->nombreempresa;
    //         $p->company_code=$value->empresa;
    //         $p->branch_code=$value->sucursal;
    //         $p->plaza_id=$value->plaza;
    //         $p->top_plaza_id=$value->plaza_sup;
    //         $p->status=$value->estatus;
    //         $p->save();
    //     }
        
    // }
}
