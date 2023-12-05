<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CatPaymentSchedule extends Model
{
	protected $table = 'cat_payment_schedule';

	use SoftDeletes;

	protected $guarded = [];
}
