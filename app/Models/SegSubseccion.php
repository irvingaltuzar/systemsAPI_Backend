<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
/**
 * Class SegSubseccion
 * 
 * @property int $subsecId
 * @property int $secId
 * @property int $subsecOrden
 * @property string $subsecDesc
 * @property string $subsecUrl
 * @property string $subsecDenegado
 * @property string $tablaDatos
 * @property bool $mostrar
 * 
 * @property SegSeccion $seg_seccion
 * @property Collection|SegAuditorium[] $seg_auditoria
 * @property Collection|SegLogin[] $seg_logins
 *
 * @package App\Models
 */
class SegSubseccion extends Model
{
	protected $table = 'seg_subseccion';
	protected $primaryKey = 'subsecId';
	public $timestamps = false;

	protected $casts = [
		'secId' => 'int',
		'subsecOrden' => 'int',
		'mostrar' => 'bool'
	];

	protected $fillable = [
		'secId',
		'subsecOrden',
		'subsecDesc',
		'subsecUrl',
		'subsecDenegado',
		'tablaDatos',
		'mostrar'
	];

	public function seg_seccion()
	{
		return $this->belongsTo(SegSeccion::class, 'secId');
	}
	public function seccion_top()
	{
		return $this->belongsTo(SegSubseccion::class,'top_seccion','subsecId');
	}
	
	public function seg_auditoria()
	{
		return $this->hasMany(SegAuditorium::class, 'subsecId');
	}

	public function seg_logins()
	{
		return $this->hasMany(SegLogin::class, 'subsecId');
	}

	public function seg_items(){
		return $this->hasMany(SegSubseccion::class,'top_seccion','subsecId')
					->with('seg_items');
	}

}
