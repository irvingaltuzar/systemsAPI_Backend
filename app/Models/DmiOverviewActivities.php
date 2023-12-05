<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiOverviewActivities extends Model
{
	use SoftDeletes;

	protected $table = 'dmiaccg_overview_activities';

	protected $guarded = [];

	protected $appends = ['overview_name'];

	protected $hidden = [
		'overview',
	];

	public function getOverviewNameAttribute()
	{
		return $this->overview->description;
	}

	public function overview()
	{
		return $this->belongsTo(CatOverview::class, 'cat_overview_id');
	}

	public function company(){
		return $this->hasOne(AccountingCompany::class,'id','accounting_company_id');
	}
}
