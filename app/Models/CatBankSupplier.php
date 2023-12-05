<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CatBankSupplier extends Model
{
	use SoftDeletes;

    protected $table = 'cat_banks_suppliers';

	protected $guarded = [];
}
