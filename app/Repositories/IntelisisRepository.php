<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntelisisRepository
{

    public function getHolidays(){
        return DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC sp_DiasFestivos");
    }

    public function getDaysTakenVacation($_personal_id){
        return DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC spVacacionesPersonal $_personal_id");
    }

	public function insertIncidence($_data){

		$type_incidence = $_data['tipo_incidencia'];
		$id_personal = $_data['personal'];
        $fechaIni = $_data['fecha_inicial'];
        $cantidad = $_data['cantidad'];
        $sucursalTrabajo = $_data['sucursal_trabajo']; //branch_code
        $empresa = $_data['empresa']; //company_code
        $referencia = $_data['referencia'];
        $observaciones = $_data['observaciones'];
        $concepto = $_data['concepto'];

        $insert_incidence= DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC sp_InsertarIncidencia '$type_incidence','$id_personal','$fechaIni',$cantidad,$sucursalTrabajo,'$empresa','$referencia','$observaciones','$concepto'");
		if(isset($insert_incidence[0]->Resultado)){
			return $insert_incidence[0]->Resultado;
		}else{
			return null;
		}

    }

	/* public function insertIncidencePruebas6000($_data){

		$type_incidence = $_data['tipo_incidencia'];
		$id_personal = $_data['personal'];
        $fechaIni = $_data['fecha_inicial'];
        $cantidad = $_data['cantidad'];
        $sucursalTrabajo = $_data['sucursal_trabajo']; //branch_code
        $empresa = $_data['empresa']; //company_code
        $referencia = $_data['referencia'];
        $observaciones = $_data['observaciones'];
        $concepto = $_data['concepto'];
        $insert_incidence= DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC [6000_Pruebas].dbo.sp_InsertarIncidenciaPermIntranet '$type_incidence','$id_personal','$fechaIni',$cantidad,$sucursalTrabajo,'$empresa','$referencia','$observaciones','$concepto'");
		if(isset($insert_incidence[0]->Resultado)){
			return $insert_incidence[0]->Resultado;
		}else{
			return null;
		}

    } */

	public function cancelIncidence($_mov_intelisis){
		$cancel_incidence= DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC Sp_CancelarIncidenciaPermIntranet $_mov_intelisis");

		return $cancel_incidence[0]->MENSAJE;
	}

    public function cancelExpiredPeriodBalance($_personal_id){
        return DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC spDMIRHSaldoVencidoPersonalVacaciones $_personal_id");
    }

    public function generatePDFPayrollInERP($_data){
        try{
            $generete_pdf = DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC spDMICFDINominaGenerarPDF ".$_data['mov'].", ".$_data['mov_id'].", ".$_data['id'].",".$_data['personal'].";");

            if($generete_pdf == null){
                
                return ['success' => 1, "data" => $generete_pdf];
            }else{
                return ["success" => 0, "data" =>$generete_pdf ,"message" => "No se generó el PDF."];
            }
        }catch(\Exception $exception){
            return ["success" => 0, "data" =>$generete_pdf ,"message" => "No se generó el PDF."];
        }
        
        
    }

    public function getMovementsToProcessed(){
        try{            
            $currentDate = Carbon::now();
		    $newDate = $currentDate->subMonths(3);// Se toma información de 6 meses hacia atras
            $start_date = $newDate->format('Y-m').'-01';
            $table= "6000_Pruebas";
            if(env("APP_ENV_IS_PROD") == 1){
                $table= "DMI_6000";
            }

            $query = "SELECT
                        a.ID as mov_intelisis,
                        b.FechaD,
                        b.Personal,
                        c.Registro2 as Rfc,
                        a.Mov,
                        a.Concepto,
                        c.ApellidoPaterno,
                        c.ApellidoMaterno,
                        c.Nombre,
                        c.CentroCostos,
                        c.FechaAntiguedad,
                        b.Cantidad
                    FROM
                        ["
                        .$table
                        ."].dbo.Nomina a
                    JOIN [$table].dbo.NominaD b ON a.ID = b.ID
                    JOIN [$table].dbo.Personal c ON b.Personal = c.Personal
                    WHERE (a.Estatus = 'borrador' OR a.Estatus = 'Procesar' OR a.Estatus = 'Vigente' OR a.Estatus like '%CONCLUIDO%')
                    AND (a.Mov like '%Permiso%' OR a.Mov like '%Vacaciones Disfrutad%' OR a.Mov like '%Vacaciones Tomadas%' OR a.Mov like '%Incapacidades%')
                    AND c.Estatus = 'ALTA'
                    And b.FechaD >= $start_date
                    ORDER BY a.Mov, c.Personal, b.FechaD";
            
            $result = DB::connection('erp_sqlsrv')->select($query);   
            
            return ["success" => 1, "data" =>$result,"message" => ""];

        }catch(\Exception $exception){
            return $exception->getMessage();
            return ["success" => 0, "data" =>"" ,"message" => "No se pudieron obtener las incidencias"];
        }
    }

}

