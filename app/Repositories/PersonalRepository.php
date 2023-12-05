<?php

namespace App\Repositories;

use App\Models\PersonalIntelisis;
use App\Models\DmiControlProcedureValidation;

class PersonalRepository
{
	public function getAccountantPersonal()
	{

		$departments = DmiControlProcedureValidation::where("key","accounting_administration_show_accountants_by-deparment")->get()->pluck("value");

		return PersonalIntelisis::whereIn('deparment',$departments)
				->where('status', 'like', "%Alta%")
				->get();
	}
}
