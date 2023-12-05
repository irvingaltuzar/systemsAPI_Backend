<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatPrivacidadDocumento
 * 
 * @property int $privacidadDocumentoId
 * @property string $descripcion
 * @property float $borrado
 * 
 * @property Collection|Documentod[] $documentods
 *
 * @package App\Models
 */
class CatPrivacidadDocumento extends Model
{
	protected $table = 'cat_privacidad_documento';
	protected $primaryKey = 'privacidadDocumentoId';
	public $timestamps = false;

	protected $casts = [
		'borrado' => 'float'
	];

	protected $fillable = [
		'descripcion',
		'borrado'
	];

	public function documentods()
	{
		return $this->hasMany(Documentod::class, 'privacidadDocumentoId');
	}
}
