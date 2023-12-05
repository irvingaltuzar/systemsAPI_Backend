<?php

/**
 * Created by Reliese Model.
 */
namespace App\Models\Accounting;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PersonalIntelisis;
use App\Models\AccountingCompany;
/**
 * Class FiscalPreclosing
 *
 * @package App\Models
 */
class FiscalPreclosing extends Model
{
	use SoftDeletes;

	protected $table = 'dmiaccg_fiscal_preclosing';

	protected $guarded = [];

	public $timestamps = true;

	protected $casts = [
		'created_at' => 'datetime:d-m-Y h:i:s',
		'updated_at' => 'datetime:d-m-Y h:i:s',
		'deleted_at' => 'datetime:d-m-Y h:i:s',


	];

	public function accounting_companies(){
		return $this->hasOne(AccountingCompany::class,'id','dmi_accounting_companies_id');
	}



}
