<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Documento
 * 
 * @property int $documentoid
 * @property int $documentoPadre
 * @property string $titulo
 * @property float $esCarpeta
 * @property float $borrado
 *
 * @package App\Models
 */
class Documento extends Model
{
	protected $table = 'documento';
	protected $primaryKey = 'documentoid';
	public $timestamps = false;

	protected $casts = [
		'documentoPadre' => 'int',
		'esCarpeta' => 'float',
		'borrado' => 'float'
	];

	protected $fillable = [
		'documentoPadre',
		'titulo',
		'esCarpeta',
		'borrado'
	];
}
