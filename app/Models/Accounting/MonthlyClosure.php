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
 * Class MonthlyClosure
 *
 * @package App\Models
 */
class MonthlyClosure extends Model
{
	use SoftDeletes;

	protected $table = 'dmiaccg_monthly_closure';

	protected $guarded = [];

	public $timestamps = true;

	protected $casts = [
		/* 'date_accounting' => 'date:d-m-Y',
		'date_fiscal' => 'date:d-m-Y',
		'date_payment' => 'date:d-m-Y', */
		'created_at' => 'datetime:d-m-Y h:i:s',
		'updated_at' => 'datetime:d-m-Y h:i:s',
		'deleted_at' => 'datetime:d-m-Y h:i:s',

	];

	protected $appends = [
		'api_document_accounting',
		'api_document_fiscal',
		'api_document_payment',
	];

	public function getApiDocumentAccountingAttribute(){
		return $this->file_accounting != null ? url($this->file_accounting) : null;
	}
	public function getApiDocumentFiscalAttribute(){
		return $this->file_fiscal != null ? url($this->file_fiscal) : null;

	}
	public function getApiDocumentPaymentAttribute(){
		return $this->file_payment != null ? url($this->file_payment) : null;

	}


	public function accounting_companies(){
		return $this->hasOne(AccountingCompany::class,'id','dmi_accounting_companies_id');
	}



}
