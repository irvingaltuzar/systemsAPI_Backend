<?php

namespace App\Services;

use App\Models\DmiabaSupplierRegistration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IntelisisSenderService
{
	public function store(Array $payload)
	{
		$now = Carbon::now();

		$sended = DB::connection('erp_sqlsrv')->table('AcumDatosIntranet')->insert([
			'Empresa' => $payload['empresa'],
			'Sucursal' => $payload['sucursal'],
			'Rama' => $payload['rama'],
			'Ejercicio' => $now->format('Y'),
			'Periodo' => $now->format('m'),
			'Cuenta' => $now->format('Ymd'),
			'Subcuenta' => $payload['subcuenta'],
			'Cargos' => $payload['cargos'],
			'Abonos' => $payload['abonos'],
			'CargosU' => $payload['cargos_u'],
			'AbonosU' => $payload['abonos_u'],
			'FechaRegistro' => $now,
			'Observaciones' => $payload['observaciones']
		]);
	}

	public function excecSp(String $rama, String $cuenta)
	{
		return DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC SP_InsertarAcumDatosIntranet $rama, $cuenta") ;
	}

	public function getPayroll(int $year)
	{
		$numempleado = Auth::user()->personal_intelisis->personal_id;

		$data = DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC Intranet.dbo.spNominasPersonal $numempleado, $year");

		$collect = collect($data);

		return $collect;
	}

	public function supplierStatus($supplier, int $status, String $supplier_code)
	{
		$files = sizeof($supplier->files) ? $supplier->files[0]->file_url : 'null';

		$folio = DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC spDMIExtraeProveedorWeb
					'$supplier_code',
					'$supplier->rfc',
					'$supplier->business_name',
					'$supplier->type_person',
					'$supplier->email',
					'$supplier->phone',
					'$supplier->contact',
					'$supplier->bank_name',
					'$supplier->bank_account',
					'$supplier->clave',
					'$supplier->bank_swift',
					'$supplier->address',
					'0',
					'0',
					'*',
					'$supplier->suburb',
					'$supplier->city',
					'$supplier->state_name',
					'$supplier->country_name',
					$supplier->cp,
					'$supplier->efo',
					'$files',
					$status"
				);

		return $folio;
	}
	public function supplierStatusRegistration($supplier)
	{
		// $files = sizeof($supplier->files) ? $supplier->files[0]->file_url : 'null';

		$folio = DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC spDMIExtraeProveedorWeb
					'',
					'$supplier',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'0',
					'0',
					'*',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					3"
				);

		return $folio;
	}
}
