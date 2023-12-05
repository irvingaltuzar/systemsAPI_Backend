<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SupplierSpecialty extends Model
{
	protected $table = 'dmiaba_supplier_specialties';

	use SoftDeletes;

	protected $guarded = [];
	protected $casts = [
		'cat_supplier_specialty' => 'integer',
	];

	protected $appends = ['specialty_name'];


	public function catSpecialty()
	{
		return $this->belongsTo(CatSupplierSpecialty::class, 'cat_supplier_specialty');
	}

	public function getSpecialtyNameAttribute()
	{
		return !!$this->catSpecialty ? $this->catSpecialty->description : 'Histórico';
	}
}
