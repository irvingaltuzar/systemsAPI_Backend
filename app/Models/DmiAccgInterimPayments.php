<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiAccgInterimPayments extends Model
{
	use SoftDeletes;

	protected $table = 'dmiaccg_interim_payments';

	protected $guarded = [];

	public function company() {
		return $this->hasOne(AccountingCompany::class,'id','accounting_company_id');
	}
}
