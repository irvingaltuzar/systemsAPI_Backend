<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Documentod
 * 
 * @property int $documentodId
 * @property int $documentoId
 * @property string $archivo
 * @property string $extension
 * @property float $tamanio
 * @property int $tipoDocumentoId
 * @property int $topicoID
 * @property int $areaId
 * @property int $privacidadDocumentoId
 * @property int $moduloId
 * @property float $numeroConsultas
 * @property float $numeroDescargas
 * @property Carbon $fechaCaptura
 * @property Carbon $fechaUltimaConsulta
 * @property Carbon $fechaUltimaDescarga
 * @property Carbon $fechaUltimaActualizacion
 * @property string $iconoDocumento
 * @property float $borrado
 * 
 * @property CatArea $cat_area
 * @property CatTopico $cat_topico
 * @property CatModulo $cat_modulo
 * @property CatPrivacidadDocumento $cat_privacidad_documento
 * @property Collection|DocumentoMetadato[] $documento_metadatos
 * @property Collection|DocumentoUbicacion[] $documento_ubicacions
 * @property Usuariodocumentod $usuariodocumentod
 *
 * @package App\Models
 */
class Documentod extends Model
{
	protected $table = 'documentod';
	protected $primaryKey = 'documentodId';
	public $timestamps = false;

	protected $casts = [
		'documentoId' => 'int',
		'tamanio' => 'float',
		'tipoDocumentoId' => 'int',
		'topicoID' => 'int',
		'areaId' => 'int',
		'privacidadDocumentoId' => 'int',
		'moduloId' => 'int',
		'numeroConsultas' => 'float',
		'numeroDescargas' => 'float',
		'borrado' => 'float'
	];

	protected $dates = [
		'fechaCaptura',
		'fechaUltimaConsulta',
		'fechaUltimaDescarga',
		'fechaUltimaActualizacion'
	];

	protected $fillable = [
		'documentoId',
		'archivo',
		'extension',
		'tamanio',
		'tipoDocumentoId',
		'topicoID',
		'areaId',
		'privacidadDocumentoId',
		'moduloId',
		'numeroConsultas',
		'numeroDescargas',
		'fechaCaptura',
		'fechaUltimaConsulta',
		'fechaUltimaDescarga',
		'fechaUltimaActualizacion',
		'iconoDocumento',
		'borrado'
	];

	public function cat_area()
	{
		return $this->belongsTo(CatArea::class, 'areaId');
	}

	public function cat_topico()
	{
		return $this->belongsTo(CatTopico::class, 'topicoID');
	}

	public function cat_modulo()
	{
		return $this->belongsTo(CatModulo::class, 'moduloId');
	}

	public function cat_privacidad_documento()
	{
		return $this->belongsTo(CatPrivacidadDocumento::class, 'privacidadDocumentoId');
	}

	public function documento_metadatos()
	{
		return $this->hasMany(DocumentoMetadato::class, 'documentodId');
	}

	public function documento_ubicacions()
	{
		return $this->hasMany(DocumentoUbicacion::class, 'documentodId');
	}

	public function usuariodocumentod()
	{
		return $this->hasOne(Usuariodocumentod::class, 'documentodId');
	}
}
