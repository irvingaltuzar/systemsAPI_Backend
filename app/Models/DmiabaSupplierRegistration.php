<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\DmiabaDocumentsSupplier;
use Illuminate\Database\Eloquent\SoftDeletes;
class DmiabaSupplierRegistration extends Model
{
	// use SoftDeletes;
    protected $table = 'dmiaba_supplier_registration';

    protected $casts = [
		'created_at' => "datetime:Y-m-d h:i:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];

	protected $guarded = [];

	protected $appends = ['responsable_name', 'responsable_mail', 'state_name', 'bank_name', 'country_name', 'status_name'];

	public function getDocumentSupplier()
	{
		return $this->belongsTo(DmiabaDocumentsSupplier::class, "id","dmiaba_supplier_registration_id")
					->where("cat_document_supplier_id",11)->where("deleted_at",null);
	}

	public function getResponsableMailAttribute()
	{
		return !!$this->responsable ? $this->responsable->mail : 'Histórico';
	}

	public function getResponsableNameAttribute()
	{
		if (!!$this->responsable) {
			$last_name = explode(" ", $this->responsable->last_name);

			return "{$this->responsable->name} {$last_name[0]}";
		} else {
			return 'Histórico';
		}
	}

	// public function getStateNameAttribute()
	// {
	// 	return $this->stateByCode->name;
	// }

	public function getStatusNameAttribute()
	{
		return $this->status == 0 ? 'Revisión' : ($this->status == 1 ? 'Aprobado' : 'Baja');
	}

	public function getCountryNameAttribute()
	{
		return !!$this->countryByCode ? $this->countryByCode->name : '';
	}

	public function getBankNameAttribute()
	{
		return $this->supplier_bank->name;
	}

	public function responsable()
	{
		$responsable = $this->belongsTo(PersonalIntelisis::class, 'user', 'usuario_ad')->where('status', 'like', '%alta%')->get();

		if (sizeof($responsable) > 0) {
			return $this->belongsTo(PersonalIntelisis::class, 'user', 'usuario_ad')->where('status', 'like', '%alta%');
		} else {
			return $this->belongsTo(PersonalIntelisis::class, 'user', 'usuario_ad')->where('status', 'like', '%baja%');
		}
	}

	public function stateByCode()
	{
		return $this->belongsTo(State::class, 'state','id');
	}

	public function countryByCode()
	{
		return $this->belongsTo(Country::class, 'country');
	}

	public function files()
	{
		return $this->hasMany(DmiabaDocumentsSupplier::class, 'dmiaba_supplier_registration_id');
	}

	public function logs()
	{
		return $this->hasMany(SegAuditoriad::class, 'id_afectado')
		->join('seg_auditoria', 'seg_auditoria.auditoriaId', '=', 'seg_auditoriad.auditoriaId')
		->join('seg_subseccion', 'seg_subseccion.subsecId', '=', 'seg_auditoria.subsecId')
		->where('seg_subseccion.secId',15);;
	}

	public function specialities()
	{
		return $this->hasMany(SupplierSpecialty::class, 'supplier_id');

	}

	public function getStateNameAttribute()
	{
		return !!$this->stateByCode ? $this->stateByCode->name : '';
	}

	public function getDocumentSupplierAll()
	{
		return $this->hasMany(DmiabaDocumentsSupplier::class,"dmiaba_supplier_registration_id", "id")->where("deleted_at",null)
		->orWhere(function($query) {
			$query->where("cat_document_supplier_id",13)
			->where("cat_document_supplier_id",18)
			->where("cat_document_supplier_id",19)
			->where("cat_document_supplier_id",20);
		});

	}

	public function supplier_bank()
	{
		return $this->belongsTo(CatBankSupplier::class, 'bank');
	}
}
