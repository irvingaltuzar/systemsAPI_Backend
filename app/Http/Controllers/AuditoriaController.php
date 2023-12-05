<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SegAuditorium as AuditoriaDet;
use App\Models\SegAuditoriad as Auditoria;
use App\Models\SegSubseccion as Subseccion;

class AuditoriaController extends Controller
{
	protected function segAuditoria(Request $request)
	{


		$ip = $this->getIPAddress();
		try {
			$subseccion = Subseccion::firstWhere('subsecDesc', $request["name_view"]);
			$auditoria = new Auditoria();
			$auditoriadet = new AuditoriaDet();
			$user = Auth::user()->usuario;

			$auditoriadet->usuario = $user;
			$auditoriadet->subsecId = $subseccion->subsecId;
			$auditoriadet->fechaHora = date('Y-m-d H:i:s');
			$auditoriadet->ip = $ip;
			$auditoriadet->evento = $request["evento"];
			$auditoriadet->error = $request["error"];
			$auditoriadet->save();
			$audetid = DB::getPdo()->lastInsertId();

			$auditoria->auditoriaId = $audetid;
			$auditoria->comentarios = $request["comentarios"];
			$auditoria->id_afectado = isset($request->id_afectado) ? $request->id_afectado : null;
			$auditoria->save();
		} catch (\Throwable $th) {
			return response()->json(['error' => 'No se pudo insertar auditoria! ' . $th]);
		}


		return response()->json(['success' => 'Evento auditoria correcto.'], 200);
	}

	protected function getTitulosDocumentosReporte()
	{

		$res = DB::select('select DISTINCT (titulo),documentoid  from documento where esCarpeta=0 and borrado=0 order by titulo');
		return response()->json($res, 200);
	}


	function getIPAddress()
	{
		//whether ip is from the share internet
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		//whether ip is from the proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		//whether ip is from the remote address
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	protected function GenerarReporteDescargas(Request $request)
	{

		$usuario = $request["usuario"];
		$documento = $request["titulo"];
		$fecha = $request["fechas"];
		$pagina = $request["pagina"];
		$limite = $request["limite"];
		$myArray = [];

		$query = "select  a.usuario as usuarioConsulta, a.fechaHora as fechaEvento, SUBSTRING(b.comentarios, 27,500) as archivoDocumento,
        d.titulo as tituloDocumento , b.comentarios
    from seg_auditoria a
        left join seg_auditoriad b on a.auditoriaId = b.auditoriaId
        left join documentod c on SUBSTRING(b.comentarios, 27,500) = c.archivo
        left join documento d on c.documentoId = d.documentoId
    where comentarios like '%Descargó este documento%' and d.titulo like'%" . $documento . "%' and a.usuario like '%" . $usuario . "%'";

		if (!empty($fecha)) {
			$fechaInicio = explode(",", $fecha)[0];
			$fechaFin = explode(",", $fecha)[1];
			$query .= " and a.fechaHora BETWEEN '" . $fechaInicio . "' and '" . $fechaFin . "'";
		}
		$res = DB::select($query . " order by a.auditoriaId desc   OFFSET " . ($pagina - 1) * $limite ." ROWS");
		$res2 = DB::select($query . " order by a.auditoriaId desc ");

		$total = count($res2);
		array_push($myArray, (object)[
			"data" => $res2,
			"total" => $total
		]);
		return response()->json($myArray, 200);
	}

	protected function GenerarReporteConsultas(Request $request)
	{

		$usuario = $request["usuario"];
		$documento = $request["titulo"];
		$fecha = $request["fechas"];
		$pagina = $request["pagina"];
		$limite = $request["limite"];
		$myArray = [];
		$query = "select a.usuario as usuarioConsulta, a.fechaHora as fechaEvento, SUBSTRING(b.comentarios, 64,500) as archivoDocumento,
        d.titulo as tituloDocumento , b.comentarios
		from seg_auditoria a
        left join seg_auditoriad b on a.auditoriaId = b.auditoriaId
        left join documentod c on SUBSTRING(b.comentarios, 64,500) = c.archivo
        left join documento d on c.documentoId = d.documentoId
		where comentarios like'%Abrio este documento%' ";
		if (!empty($usuario)) {
			$query .= "and a.usuario like '%" . $usuario . "%'";
		}
		if (!empty($documento)) {
			$query .= "and d.titulo like'%" . $documento . "%'";
		}
		if (!empty($fecha)) {
			$fechaInicio = explode(",", $fecha)[0];
			$fechaFin = explode(",", $fecha)[1];
			$query .= "and a.fechaHora BETWEEN '" . $fechaInicio . "' and '" . $fechaFin . "'";
		}
		$res = DB::select($query . " order by a.auditoriaId desc  OFFSET " . ($pagina - 1) * $limite." ROWS");
		$res2 = DB::select($query . " order by a.auditoriaId desc ");
		$total = count($res2);
		array_push($myArray, (object)[
			"data" => $res,
			"total" => $total
		]);

		return response()->json($myArray, 200);
	}

	protected function GenerarReporteBusquedas(Request $request)
	{

		$usuario = $request["usuario"];
		$pagina = $request["pagina"];
		$limite = $request["limite"];
		$myArray = [];

		$query = "select a.usuario as usuarioConsulta, SUBSTRING(b.comentarios, 28,500) as cadenaBusqueda, count(SUBSTRING(b.comentarios, 28,500)) as total
        from seg_auditoria a
            left join seg_auditoriad b on a.auditoriaId = b.auditoriaId
        where comentarios like'%Realizando esta búsqueda%' and COALESCE(SUBSTRING(b.comentarios, 28,500), '') != ''";
		if (!empty($usuario)) {
			$query .= "and a.usuario like '%" . $usuario . "%'";
		}

		$res = DB::select($query . " group by a.usuario, SUBSTRING(b.comentarios, 28,500)
        order by a.usuario, count(SUBSTRING(b.comentarios, 28,500)) desc  OFFSET " . ($pagina - 1) * $limite." ROWS");
		$res2 = DB::select($query . "group by a.usuario, SUBSTRING(b.comentarios, 28,500)
        order by a.usuario, count(SUBSTRING(b.comentarios, 28,500)) ");
		$total = count($res2);
		array_push($myArray, (object)[
			"data" => $res,
			"total" => $total
		]);

		return response()->json($myArray, 200);
	}
}
