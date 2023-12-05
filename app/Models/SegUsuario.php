<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * Class SegUsuario
 *
 * @property int $usuarioId
 * @property string $nombre
 * @property string $apePat
 * @property string $apeMat
 * @property string $usuario
 * @property string $password
 * @property float $roles
 * @property float $borrado
 *
 * @property Collection|SegLogin[] $seg_logins
 * @property Usuariodocumentod $usuariodocumentod
 *
 * @package App\Models
 */
class SegUsuario extends Authenticatable
{
use HasApiTokens, Notifiable;

	protected $table = 'seg_usuarios';
	protected $primaryKey = 'usuarioId';
	public $timestamps = false;

	protected $guarded = [];

	protected $casts = [
		'roles' => 'float',
		'borrado' => 'float'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'nombre',
		'apePat',
		'apeMat',
		'usuario',
		'password',
		'roles',
		'borrado'
	];

	protected $appends = ['personal_intelisis'];

	public function seg_logins()
	{
		return $this->hasMany(SegLogin::class, 'usuarioId');
	}

	public function usuariodocumentod()
	{
		return $this->hasOne(Usuariodocumentod::class, 'usuarioId');
	}

	public function getPersonalIntelisisAttribute()
	{
		return PersonalIntelisis::where('usuario_ad', 'like' , "%$this->usuario%")
				->where('status', 'like', "%Alta%")
				->first();
	}
}
