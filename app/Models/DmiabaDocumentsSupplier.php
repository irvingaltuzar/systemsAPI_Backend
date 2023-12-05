<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class DmiabaDocumentsSupplier extends Model
{
	use SoftDeletes;

	protected $guarded = [];

    protected $table = 'dmiaba_documents_supplier';

	protected $appends = ['file_url'];

	protected $casts = [
		'cat_document_supplier_id' => 'int',
		'dmiaba_supplier_registration_id' => 'int',

	];

	public function getFileUrlAttribute()
	{

		return $this->cat_document_supplier_id !== 11 ? Storage::disk('public')->url("Proveedores/{$this->name}") : Storage::disk('public')->url("Proveedores/EFO/{$this->name}");
	}
}
