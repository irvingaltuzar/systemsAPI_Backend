<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CatEAccountingStatus extends Model
{
	use SoftDeletes;

	protected $guarded = [];
}
