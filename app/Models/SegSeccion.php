<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\SegSubseccion;
/**
 * Class SegSeccion
 * 
 * @property int $secId
 * @property string $secDesc
 * @property float $secOrden
 * @property float $secPocision
 * 
 * @property Collection|SegSubseccion[] $seg_subseccions
 *
 * @package App\Models
 */
class SegSeccion extends Model
{
	protected $table = 'seg_seccion';
	protected $primaryKey = 'secId';
	public $timestamps = false;

	protected $casts = [
		'secOrden' => 'int',
		'secPocision' => 'int'
	];

	protected $fillable = [
		'secDesc',
		'secOrden',
		'secPocision'
	];

	public function seg_subseccions()
	{
		return $this->hasMany(SegSubseccion::class, 'secId');
	}
	public function getSubseccions()
	{
		return $this->hasMany(SegSubseccion::class, 'secId')
		->where("mostrar",1)->where("public",0)->orderBy("subsecOrden");
	}

	public function seg_items(){
		return $this->hasMany(SegSubseccion::class,'top_seccion','subsecId')
					->with('seg_items')
					->orderBy("subsecOrden");
	}
}
