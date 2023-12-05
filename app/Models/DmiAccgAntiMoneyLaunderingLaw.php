<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountingCompany;

class DmiAccgAntiMoneyLaunderingLaw extends Model
{

    protected $table = 'dmiaccg_anti_money_laundering_law';


    public function getCompany()
	{
		return $this->belongsTo(AccountingCompany::class, 'dmiaccg_company_id','id');
	}
}
