<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SegLogin
 * 
 * @property int $loginId
 * @property int $usuarioId
 * @property int $subsecId
 * @property string $loginUsr
 * @property string $loginCrud
 * 
 * @property SegUsuario $seg_usuario
 * @property SegSubseccion $seg_subseccion
 *
 * @package App\Models
 */
class SegLogin extends Model
{
	protected $table = 'seg_login';
	protected $primaryKey = 'loginId';
	public $timestamps = false;

	protected $casts = [
		'usuarioId' => 'int',
		'subsecId' => 'int'
	];

	protected $fillable = [
		'usuarioId',
		'subsecId',
		'loginUsr',
		'loginCrud'
	];

	public function seg_usuario()
	{
		return $this->belongsTo(SegUsuario::class, 'usuarioId');
	}

	public function seg_subseccion()
	{
		return $this->belongsTo(SegSubseccion::class, 'subsecId');
	}
}
