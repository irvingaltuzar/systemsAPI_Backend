<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


/**
 * Class DmiControlPlazaSustitution
 *
 * @package App\Models
 */
class DmiControlPlazaSustitution extends Model
{
	use SoftDeletes;

	protected $table = 'dmicontrol_plaza_substitution';

	public $timestamps = true;

	protected $casts = [
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];

	public function replaced(){
		return $this->hasOne(PersonalIntelisis::class,'plaza_id','plaza_id')->where('status','ALTA');
	}

	public function replace(){
		return $this->hasOne(PersonalIntelisis::class,'plaza_id','substitute_plaza_id',)->where('status','ALTA');
	}

}
