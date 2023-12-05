<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiEAccounting extends Model
{
	use SoftDeletes;

	protected $table = 'dmiaccg_e_accountings';

	protected $guarded = [];

	protected $appends = ['status_name'];

	protected $hidden = [
		'status',
	];

	public function getStatusNameAttribute()
	{
		return $this->status->description;
	}

	public function status()
	{
		return $this->belongsTo(CatEAccountingStatus::class, 'cat_e_accounting_status_id');
	}

	public function company(){
		return $this->hasOne(AccountingCompany::class,'id','accounting_company_id');
	}
}
