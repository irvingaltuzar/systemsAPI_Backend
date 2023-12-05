<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatTopico
 * 
 * @property int $topicoId
 * @property string $descripcion
 * @property float $borrado
 * 
 * @property Collection|Documentod[] $documentods
 *
 * @package App\Models
 */
class CatTopico extends Model
{
	protected $table = 'cat_topico';
	protected $primaryKey = 'topicoId';
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
		return $this->hasMany(Documentod::class, 'topicoID');
	}
}
