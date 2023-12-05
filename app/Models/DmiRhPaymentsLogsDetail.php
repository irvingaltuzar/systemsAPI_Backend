<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiRhPaymentsLogsDetail extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_payments_logs_details';

	protected $guarded = [];
}
