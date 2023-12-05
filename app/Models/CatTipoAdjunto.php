<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CatTipoAdjunto
 * 
 * @property int $tipoAdjuntoId
 * @property string $descripcion
 * @property string $extension
 * @property string $mimeType
 * @property float $tamanioPermitido
 * @property float $activado
 * @property float $borrado
 *
 * @package App\Models
 */
class CatTipoAdjunto extends Model
{
	protected $table = 'cat_tipo_adjunto';
	protected $primaryKey = 'tipoAdjuntoId';
	public $timestamps = false;

	protected $casts = [
		'tamanioPermitido' => 'float',
		'activado' => 'float',
		'borrado' => 'float'
	];

	protected $fillable = [
		'descripcion',
		'extension',
		'mimeType',
		'tamanioPermitido',
		'activado',
		'borrado'
	];
}
