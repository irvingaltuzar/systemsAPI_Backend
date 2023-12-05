<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VwUsuariosUbicacion
 * 
 * @property int $usuarioId
 * @property string $nombre
 * @property string $apePat
 * @property string $apeMat
 * @property string $usuario
 * @property string $password
 * @property float $roles
 * @property float $borrado
 * @property string|null $ubicacion
 *
 * @package App\Models
 */
class VwUsuariosUbicacion extends Model
{
	protected $table = 'vw_usuarios_ubicacion';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'usuarioId' => 'int',
		'roles' => 'float',
		'borrado' => 'float'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'usuarioId',
		'nombre',
		'apePat',
		'apeMat',
		'usuario',
		'password',
		'roles',
		'borrado',
		'ubicacion'
	];
}
