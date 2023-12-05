<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatModulo
 * 
 * @property int $moduloId
 * @property string $descripcion
 * @property float $borrado
 * 
 * @property Collection|Documentod[] $documentods
 *
 * @package App\Models
 */
class CatModulo extends Model
{
	protected $table = 'cat_modulo';
	protected $primaryKey = 'moduloId';
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
		return $this->hasMany(Documentod::class, 'moduloId');
	}
}
