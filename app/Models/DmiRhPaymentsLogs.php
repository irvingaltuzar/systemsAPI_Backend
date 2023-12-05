<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DmiRhPaymentsLogs extends Model
{
	use SoftDeletes;

	protected $table = 'dmirh_payments_logs';

	protected $guarded = [];

	public function responsable()
	{
		return $this->belongsTo(SegUsuario::class, 'seg_usuario_id');
	}

	public function detail()
	{
		return $this->hasMany(DmiRhPaymentsLogsDetail::class, 'payment_log_id');
	}
}
