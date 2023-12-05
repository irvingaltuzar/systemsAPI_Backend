<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PersonalIntelisis as PersonalIntelisis;
use App\Models\DmiRh\DmirhPersonalTime;
use App\Models\DmiRh\DmirhPersonalTimeDetail;
use App\Models\SegUsuario as Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\Repositories\IntelisisRepository;
class UpdatePersonalIntelisis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:personalIntelisis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualización informacion Personal Intelisis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $stmt= DB::connection('erp_sqlsrv')->select("EXECUTE [dbo].[SP_DatosPersonalParaParaWEB] '',0,'1600/01/01','2997/12/31'");
            
      // return $stmt;
      foreach ($stmt as $res) {

               $per = PersonalIntelisis::where('personal_id',$res->Personal)->first();

               if(isset($per) || $per !=""){

                $per->personal_id=$res->Personal;
                $per->previous_personal_id=$res->CodigoAnterior;
                $per->name=$res->Nombre;
                $per->last_name=$res->Apellidos;
                $per->birth=$res->FechaNacimiento;
                $per->sex=$res->Genero;
                $per->total_vacation_days=intval($res->DiasVacTotal);
                if(isset($res->eMailEmpresa) || $res->eMailEmpresa!=""){
                    $per->email=$res->eMailEmpresa;
                    }
                    $per->extension=$res->Extension;
                    $per->photo=$res->DirFoto;
                    if(isset($res->Puesto) || $res->Puesto!=""){
                    $per->position_company=$res->Puesto;
                    }
                    if(isset($res->Puesto_Especifico) || $res->Puesto_Especifico!=""){
                    $per->position_company_full=$res->Puesto_Especifico;
                    }
                    if(isset($res->Departamento) || $res->Departamento!=""){
                    $per->deparment=$res->Departamento;
                    }
                    $per->date_admission=$res->FechaIngreso;
                    $per->antiquity_date=$res->FechaAntiguedad;
                    if(isset($res->Ubicacion) || $res->Ubicacion!=""){
                    $per->location=$res->Ubicacion;
                    }
                    $per->payment_period=$res->PeriodoTipo;
                    $per->company_name=$res->NombreEmpresa;
                    $per->company_code=$res->Empresa;
                    $per->branch_code=$res->Sucursal;
                    if(isset($res->Plaza) || $res->Plaza!=""){
                    $per->plaza_id=$res->Plaza;
                    }

                    if(isset($res->PlazaSup) || $res->PlazaSup!=""){
                    $per->top_plaza_id=$res->PlazaSup;
                    }
                    $per->status=$res->Estatus;
                if(isset($res->usuarioAD) || $res->usuarioAD!=""){
                $per->usuario_ad= strtolower($res->usuarioAD);
                }
                 $per->rfc=$res->RFC;

                    $per->save();


               }else{
                if($res->usuarioAD!="" || $res->usuarioAD!=null){
                $personal = new PersonalIntelisis();
                $personal->personal_id=$res->Personal;
                $personal->name=$res->Nombre;
                $personal->last_name=$res->Apellidos;
                $personal->birth=$res->FechaNacimiento;
                $personal->sex=$res->Genero;
                $personal->email=$res->eMailEmpresa;
                $personal->extension=$res->Extension;
                $personal->photo=$res->DirFoto;
                $personal->position_company=$res->Puesto;
                $personal->position_company_full=$res->Puesto_Especifico;
                $personal->deparment=$res->Departamento;
                $personal->date_admission=$res->FechaIngreso;
                $personal->antiquity_date=$res->FechaAntiguedad;
                $personal->location=$res->Ubicacion;
                $personal->payment_period=$res->PeriodoTipo;
                $personal->company_name=$res->NombreEmpresa;
                $personal->company_code=$res->Empresa;
                $personal->branch_code=$res->Sucursal;
                $personal->plaza_id=$res->Plaza;
                $personal->top_plaza_id=$res->PlazaSup;
                $personal->status=$res->Estatus;
                $personal->usuario_ad=strtolower($res->usuarioAD);
                $personal->previous_personal_id=$res->CodigoAnterior;
                $personal->total_vacation_days= intval($res->DiasVacTotal);
                $personal->rfc=$res->RFC;
                $personal->save();

     
                    //proceso creacion de Horario personal default
                    try{
                    //Creacion de usuario
                    // Separamos el apellido en un array usando el espacio como separador
                    $apellidos_array = explode(" ", $res->Apellidos);

                    // El primer elemento del array es el apellido paterno
                    $apellido_paterno = $apellidos_array[0];

                    // Inicializamos el apellido materno como una cadena vacía
                    $apellido_materno = "";

                    // Recorremos los elementos restantes del array y los concatenamos al apellido materno
                    for ($i = 1; $i < count($apellidos_array); $i++) {
                        $apellido_materno .= $apellidos_array[$i] . " ";
                    }
                    $usuario= new Usuario();
                    $usuario->nombre= $res->Nombre;
                    $usuario->apePat= $apellido_paterno;
                    $usuario->apeMat= $apellido_materno;
                    $usuario->usuario= $res->usuarioAD;
                    $usuario->password= bcrypt('OupQrqJT');
                    $usuario->roles= 0;
                    $usuario->borrado= 0;
                    $usuario->save();
                    //Creacion de Horario
                    $personaltime = new DmirhPersonalTime();
                    $personaltime->user = $res->usuarioAD;
                    $personaltime->dmirh_cat_time_status_id = 1;
                    $personaltime->start_date = Carbon::now();
                    $personaltime->approved_by = "Sistemas";
                    $personaltime->approved_date= Carbon::now();
                    $personaltime->active =1;
                    $personaltime->save();
                    $personaltimeId=DB::getPdo()->lastInsertId();

                   $jor='Lunes,Martes,Miercoles,Jueves,Viernes';
                    $arrJornada= explode(",",$jor);

                    foreach ( $arrJornada as $value) {
                      $personaltimeDetail = new DmirhPersonalTimeDetail();
                      $personaltimeDetail->dmirh_personal_time_id= $personaltimeId;

                    //Detalle de Horario
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
                }
               }
        }
    }
}
