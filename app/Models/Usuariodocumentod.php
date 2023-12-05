<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Usuariodocumentod
 * 
 * @property int $usuarioId
 * @property int $documentodId
 * 
 * @property Documentod $documentod
 * @property SegUsuario $seg_usuario
 *
 * @package App\Models
 */
class Usuariodocumentod extends Model
{
	protected $table = 'usuariodocumentod';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'usuarioId' => 'int',
		'documentodId' => 'int'
	];

	protected $fillable = [
		'usuarioId',
		'documentodId'
	];

	public function documentod()
	{
		return $this->belongsTo(Documentod::class, 'documentodId');
	}

	public function seg_usuario()
	{
		return $this->belongsTo(SegUsuario::class, 'usuarioId');
	}
}
