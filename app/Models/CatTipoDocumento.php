<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CatTipoDocumento
 * 
 * @property int $tipoDocumentoId
 * @property float $esDeSistema
 * @property string $nombreTipoDocumento
 * @property float $borrado
 *
 * @package App\Models
 */
class CatTipoDocumento extends Model
{
	protected $table = 'cat_tipo_documento';
	protected $primaryKey = 'tipoDocumentoId';
	public $timestamps = false;

	protected $casts = [
		'esDeSistema' => 'float',
		'borrado' => 'float'
	];

	protected $fillable = [
		'esDeSistema',
		'nombreTipoDocumento',
		'borrado'
	];
}
