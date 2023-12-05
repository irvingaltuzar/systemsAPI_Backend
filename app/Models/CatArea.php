<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatArea
 * 
 * @property int $areaId
 * @property string $descripcion
 * @property float $borrado
 * 
 * @property Collection|Documentod[] $documentods
 *
 * @package App\Models
 */
class CatArea extends Model
{
	protected $table = 'cat_area';
	protected $primaryKey = 'areaId';
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
		return $this->hasMany(Documentod::class, 'areaId');
	}
}
