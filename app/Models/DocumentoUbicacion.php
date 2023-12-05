<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DocumentoUbicacion
 * 
 * @property int $documentoUbicacionId
 * @property int $documentodId
 * @property int $ubicacionId
 * @property float $borrado
 * 
 * @property CatUbicacion $cat_ubicacion
 * @property Documentod $documentod
 *
 * @package App\Models
 */
class DocumentoUbicacion extends Model
{
	protected $table = 'documento_ubicacion';
	protected $primaryKey = 'documentoUbicacionId';
	public $timestamps = false;

	protected $casts = [
		'documentodId' => 'int',
		'ubicacionId' => 'int',
		'borrado' => 'float'
	];

	protected $fillable = [
		'documentodId',
		'ubicacionId',
		'borrado'
	];

	public function cat_ubicacion()
	{
		return $this->belongsTo(CatUbicacion::class, 'ubicacionId');
	}

	public function documentod()
	{
		return $this->belongsTo(Documentod::class, 'documentodId');
	}
}
