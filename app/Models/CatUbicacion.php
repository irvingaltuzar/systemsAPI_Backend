<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatUbicacion
 * 
 * @property int $ubicacionId
 * @property string $descripcion
 * @property float $borrado
 * 
 * @property Collection|DocumentoUbicacion[] $documento_ubicacions
 *
 * @package App\Models
 */
class CatUbicacion extends Model
{
	protected $table = 'cat_ubicacion';
	protected $primaryKey = 'ubicacionId';
	public $timestamps = false;

	protected $casts = [
		'borrado' => 'float'
	];

	protected $fillable = [
		'descripcion',
		'borrado'
	];

	public function documento_ubicacions()
	{
		return $this->hasMany(DocumentoUbicacion::class, 'ubicacionId');
	}
}
