<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class WorkDays extends Model
{
	protected $table = 'cat_work_days';

	use SoftDeletes;

	protected $guarded = [];
}
