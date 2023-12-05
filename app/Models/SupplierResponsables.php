<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class SupplierResponsables extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $table = 'cat_supplier_responsables';

}
