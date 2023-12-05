<?php

namespace App\Models;

use App\Models\DmiRh\DmirhPersonalTime;
use App\Models\VwDmiPersonalPlaza;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PersonalIntelisis extends Model
{
	protected $table = 'personal_intelisis';

	protected $dates = [
		'birth',
		'date_admission',
	];


	protected $casts = [
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'birth' => 'date:Y-m-d',
		'date_admission' => 'date:Y-m-d',
	];

	protected $appends = ['full_name'];

	public function getFullNameAttribute()
	{
		return "$this->name $this->last_name";
	}

	// public function commanding_staff_res(){
	// 	return $this->hasMany(PersonalIntelisis::class,'top_plaza_id','plaza_id')
	// 				->with('commanding_staff')
	// 				->where('status','ALTA');
	// }
	public function commanding_staff(){
		return $this->hasMany(PersonalIntelisis::class,'top_plaza_id','plaza_id')
					->with('commanding_staff')
					->where('status','ALTA');
	}

	// public function commanding_staff_all(){
	// 	return $this->hasMany(PersonalIntelisis::class,'top_plaza_id','plaza_id')
	// 	->with('commanding_staff_all');
	// }
	// public function commanding_staff_All_resp(){
	// 	return $this->hasMany(PersonalIntelisis::class,'top_plaza_id','plaza_id');
	// }

	public function staffall(){
		return $this->hasMany(VwDmiPersonalPlaza::class,'top_plaza_id','plaza_id')
		->with('staffall');
	}

	public function dmirh_personal_time(){
		return $this->hasOne(DmirhPersonalTime::class,'user','usuario_ad')
				->with('dmirh_personal_time_details')
				->where('dmirh_cat_time_status_id',1)
				->where('active',1)
				->orderBy('id','desc')
				->where('deleted',0)
				->latest();

	}

	public function immediate_boss(){
		return $this->hasOne(PersonalIntelisis::class,'plaza_id','top_plaza_id')
							->where('status','ALTA');
	}

	public function get_higher(){
		return $this->hasOne(PersonalIntelisis::class,'plaza_id','top_plaza_id')
					->with('get_higher')
					->where('status','ALTA')
					->where('plaza_id','!=','');
	}
	public function get_higher_cop(){
		return $this->hasOne(VwDmiPersonalPlaza::class,'plaza_id','top_plaza_id')
		->with('get_higher_cop');
	}


}
