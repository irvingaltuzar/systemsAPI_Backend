<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AccountingCompany extends Model
{
	use SoftDeletes;

	protected $table = 'dmiaccg_accounting_companies';

	protected $guarded = [];

	protected $appends = ['work_station_name', 'erp_name', 'accountant_name', 'manager_name'];

	protected $hidden = [
		'workStation',
		'erp',
		'accountant',
		'manager'
	];

	public function getWorkStationNameAttribute()
	{
		return $this->workStation->description;
	}

	public function getManagerNameAttribute()
	{

		if (!!$this->manager) {
			$last_name = explode(" ", $this->manager->last_name);

			return "{$this->manager->name} {$last_name[0]}";
		} else {
			return 'Histórico';
		}

		// $last_name = explode(" ", $this->manager->last_name);

		// return "{$this->manager->name} {$last_name[0]}";
	}

	public function getAccountantNameAttribute()
	{

		if (!!$this->accountant) {
			$last_name = explode(" ", $this->accountant->last_name);

			return "{$this->accountant->name} {$last_name[0]}";
		} else {
			return 'Histórico';
		}

		// $last_name = explode(" ", $this->accountant->last_name);

		// return "{$this->accountant->name} {$last_name[0]}";
	}

	public function getErpNameAttribute()
	{
		return $this->erp->description;
	}

	public function workStation()
	{
		return $this->belongsTo(CatWorkStation::class, 'cat_work_station_id');
	}

	public function erp()
	{
		return $this->belongsTo(CatErp::class, 'cat_erp_id');
	}

	public function manager()
	{
		return $this->belongsTo(PersonalIntelisis::class, 'manager_id', 'usuario_ad');
	}

	public function accountant()
	{
		return $this->belongsTo(PersonalIntelisis::class, 'accountant_id', 'usuario_ad');
	}

	public function electronicAccounting()
	{
		return $this->hasMany(DmiEAccounting::class, 'accounting_company_id');
	}

	public function overviewsAccounting()
	{
		return $this->hasMany(DmiOverviewActivities::class, 'accounting_company_id');
	}

	public function diot()
	{
		return $this->hasMany(DmiaccgDiot::class, 'accounting_company_id');
	}

	public function dyp()
	{
		return $this->hasMany(DmiaccgDyp::class, 'accounting_company_id');
	}
}
