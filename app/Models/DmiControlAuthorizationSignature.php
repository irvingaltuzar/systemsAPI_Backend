<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DmiControlAuthorizationSignature
 * 
 * @property int $id
 * @property string $plaza_id
 * @property int $subsecId
 * @property int $dmi_control_process_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property DmiControlProcess $dmi_control_process
 *
 * @package App\Models
 */
class DmiControlAuthorizationSignature extends Model
{
	use SoftDeletes;
	protected $table = 'dmicontrol_authorization_signatures';

	protected $casts = [
		'subsecId' => 'int',
		'dmi_control_process_id' => 'int',
		'created_at' => "datetime:Y-m-d h:m:s",
		'updated_at' => 'datetime:Y-m-d h:i:s',
		'deleted_at' => 'datetime:Y-m-d h:i:s',
	];

	protected $fillable = [
		'plaza_id',
		'subsecId',
		'dmi_control_process_id'
	];
	
	public $timestamps = true;

	

	public function dmi_control_process()
	{
		return $this->belongsTo(DmiControlProcess::class);
	}
}
