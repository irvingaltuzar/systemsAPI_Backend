<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SegAuditorium
 *
 * @property int $auditoriaId
 * @property string $usuario
 * @property int $subsecId
 * @property Carbon $fechaHora
 * @property string $ip
 * @property string|null $evento
 * @property int|null $error
 *
 * @property SegSubseccion $seg_subseccion
 * @property Collection|SegAuditoriad[] $seg_auditoriads
 *
 * @package App\Models
 */
class SegAuditorium extends Model
{
	protected $table = 'seg_auditoria';
	protected $primaryKey = 'auditoriaId';
	public $timestamps = false;

	protected $casts = [
		'subsecId' => 'int',
		'error' => 'int'
	];

	protected $dates = [
		'fechaHora'
	];

	protected $fillable = [
		'usuario',
		'subsecId',
		'fechaHora',
		'ip',
		'evento',
		'error'
	];

	protected $guarded = [];

	protected $appends = ['responsable_name'];

	protected $hidden = ['responsable'];

	public function getResponsableNameAttribute()
	{
		if (!!$this->responsable) {
			$last_name = explode(" ", $this->responsable->last_name);

			return "{$this->responsable->name} {$last_name[0]}";
		} else {
			return 'Histórico';
		}
	}

	public function seg_subseccion()
	{
		return $this->belongsTo(SegSubseccion::class, 'subsecId');
	}

	public function seg_auditoriads()
	{
		return $this->hasMany(SegAuditoriad::class, 'auditoriaId');
	}

	public function responsable()
	{
		return $this->belongsTo(PersonalIntelisis::class, 'usuario', 'usuario_ad');
	}
}
