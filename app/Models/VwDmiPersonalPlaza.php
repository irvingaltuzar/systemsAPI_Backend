<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VwDmiPersonalPlaza extends Model
{
    protected $table = 'vw_DMIPersonalPlaza';
    public $incrementing = false;
	public $timestamps = false;

	public function get_higher_cop(){
		return $this->hasOne(VwDmiPersonalPlaza::class,'plaza_id','top_plaza_id')
		->with('get_higher_cop');
	}
    public function commanding_staff(){
		return $this->hasMany(VwDmiPersonalPlaza::class,'top_plaza_id','plaza_id')
					->with('commanding_staff');
					
	}
    public function staffall(){
		return $this->hasMany(VwDmiPersonalPlaza::class,'top_plaza_id','plaza_id')
        ->with('staffall');
	}
}
