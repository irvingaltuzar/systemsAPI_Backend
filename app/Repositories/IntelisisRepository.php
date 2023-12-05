<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

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

	public function cancelIncidence($_mov_intelisis){
		$cancel_incidence= DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC Sp_CancelarIncidenciaPermIntranet $_mov_intelisis");

		return $cancel_incidence[0]->MENSAJE;
	}

}

