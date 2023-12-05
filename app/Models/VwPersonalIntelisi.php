<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VwPersonalIntelisi
 * 
 * @property string $idpersonal
 * @property string|null $nombre
 * @property string|null $apellidos
 * @property string|null $puesto
 * @property string|null $correo
 * @property string|null $telefono
 * @property string|null $extension
 * @property Carbon|null $fechaingreso
 * @property string|null $descripcion
 * @property Carbon|null $fechanac
 * @property string|null $grupo
 * @property string|null $foto
 * @property string|null $departamento
 * @property string|null $reportaa
 * @property string|null $nombreempresa
 * @property string|null $empresa
 * @property string|null $sucursal
 * @property string|null $emailempresa
 * @property Carbon|null $fechanacimiento
 * @property string|null $genero
 * @property string|null $estatus
 * @property Carbon|null $fechaantiguedad
 * @property Carbon|null $fechabaja
 * @property string|null $idpersonal_n
 * @property string|null $idpersonal_anterior
 * @property string|null $puesto_intelisis
 * @property string|null $UEN
 * @property string|null $fuente
 * @property string|null $periodo_tipo
 * @property string|null $jornada
 * @property string|null $entrada_real
 * @property string|null $entrada_ajuste
 * @property string|null $entrada_anterior
 * @property string|null $comida_real
 * @property string|null $comida_ajuste
 * @property string|null $comida_anterior
 * @property string|null $salida_real
 * @property string|null $salida_ajuste
 * @property string|null $salida_anterior
 * @property Carbon|null $ajuste_horario
 * @property string|null $id_plaza
 * @property string|null $ubicacion
 *
 * @package App\Models
 */
class VwPersonalIntelisi extends Model
{
	protected $table = 'vw_personal_intelisis';
	public $incrementing = false;
	public $timestamps = false;

	protected $dates = [
		'fechaingreso',
		'fechanac',
		'fechanacimiento',
		'fechaantiguedad',
		'fechabaja',
		'ajuste_horario'
	];

	protected $fillable = [
		'idpersonal',
		'nombre',
		'apellidos',
		'puesto',
		'correo',
		'telefono',
		'extension',
		'fechaingreso',
		'descripcion',
		'fechanac',
		'grupo',
		'foto',
		'departamento',
		'reportaa',
		'nombreempresa',
		'empresa',
		'sucursal',
		'emailempresa',
		'fechanacimiento',
		'genero',
		'estatus',
		'fechaantiguedad',
		'fechabaja',
		'idpersonal_n',
		'idpersonal_anterior',
		'puesto_intelisis',
		'UEN',
		'fuente',
		'periodo_tipo',
		'jornada',
		'entrada_real',
		'entrada_ajuste',
		'entrada_anterior',
		'comida_real',
		'comida_ajuste',
		'comida_anterior',
		'salida_real',
		'salida_ajuste',
		'salida_anterior',
		'ajuste_horario',
		'id_plaza',
		'ubicacion'
	];
}
